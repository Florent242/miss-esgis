<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSandbox;
use App\Models\Miss;
use App\Models\Transaction;
use App\Models\Vote;
use App\Services\MoMoPaymentService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SandboxPaymentController extends Controller
{
    protected $momoService;

    public function __construct(MoMoPaymentService $momoService)
    {
        $this->momoService = $momoService;
    }

    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'miss_id' => 'required|exists:misses,id',
            'operator' => 'required|in:mtn,moov,celtiis',
            'phone_number' => 'required|string|min:8|max:20',
            'amount' => 'required|numeric|min:100',
            'vote_count' => 'required|integer|min:1'
        ]);

        $miss = Miss::findOrFail($validated['miss_id']);

        if ($miss->statut !== 'active') {
            return response()->json(['error' => 'Cette candidate ne peut pas recevoir de votes'], 400);
        }

        // Générer une référence unique (lowercase pour MTN API)
        $reference = strtolower(Str::uuid()->toString());

        // Client paie toujours 100 FCFA par vote (aucun frais supplémentaire)
        $clientAmount = $validated['amount'];

        // Créer la transaction en attente
        $payment = PaymentSandbox::create([
            'reference' => $reference,
            'miss_id' => $validated['miss_id'],
            'operator' => $validated['operator'],
            'phone_number' => $validated['phone_number'],
            'amount' => $clientAmount,
            'vote_count' => $validated['vote_count'],
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'expires_at' => now()->addMinutes(5)
        ]);

        Log::info('Payment sandbox initiated', [
            'reference' => $reference,
            'operator' => $validated['operator'],
            'amount' => $clientAmount
        ]);

        // Déclencher le débit MoMo direct
        $result = $this->momoService->requestToPay(
            $validated['operator'],
            $validated['phone_number'],
            $clientAmount,
            $reference
        );

        if (!$result['success']) {
            $payment->status = 'failed';
            $payment->save();
            
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Erreur lors de l\'initialisation du paiement'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'reference' => $reference,
            'operator' => $validated['operator'],
            'amount' => $clientAmount,
            'message' => 'Vérifiez votre téléphone pour confirmer le paiement'
        ]);
    }

    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string'
        ]);

        $payment = PaymentSandbox::where('reference', $validated['reference'])->first();

        if (!$payment) {
            return response()->json(['error' => 'Transaction non trouvée'], 404);
        }

        // Vérifier le statut via l'API MoMo
        if ($payment->status === 'pending') {
            $apiStatus = $this->momoService->checkStatus($payment->operator, $payment->reference);
            
            // Ne confirmer automatiquement QUE en production (pas en sandbox)
            if (env('MTN_MOMO_ENVIRONMENT') === 'production' && 
                isset($apiStatus['status']) && $apiStatus['status'] === 'successful') {
                // Confirmer le paiement
                $this->confirmPayment($payment);
                $payment->refresh();
            } elseif (isset($apiStatus['status']) && in_array($apiStatus['status'], ['failed', 'rejected'])) {
                $payment->status = 'failed';
                $payment->save();
            }
        }

        if ($payment->isExpired() && $payment->status === 'pending') {
            $payment->status = 'expired';
            $payment->save();
        }

        return response()->json([
            'status' => $payment->status,
            'reference' => $payment->reference,
        ]);
    }

    private function confirmPayment($payment)
    {
        try {
            DB::beginTransaction();

            $payment->status = 'confirmed';
            $payment->save();

            // Créer la transaction officielle
            $transaction = Transaction::create([
                'reference' => $payment->reference,
                'miss_id' => $payment->miss_id,
                'montant' => $payment->amount,
                'methode' => 'momo_' . $payment->operator,
                'statut' => 'completed',
            ]);

            // Créer les votes
            for ($i = 0; $i < $payment->vote_count; $i++) {
                Vote::create([
                    'miss_id' => $payment->miss_id,
                    'transaction_id' => $transaction->id,
                    'moyen_paiement' => 'momo_' . $payment->operator,
                    'montant' => 100,
                ]);
            }

            DB::commit();

            Log::info('Payment confirmed', [
                'reference' => $payment->reference,
                'votes_created' => $payment->vote_count
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming payment', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id
            ]);
            throw $e;
        }
    }

    public function getOperators()
    {
        return response()->json([
            'operators' => [
                [
                    'code' => 'mtn',
                    'name' => 'MTN Mobile Money',
                    'color' => '#FFCC00',
                    'available' => !empty(env('MTN_MOMO_API_USER'))
                ],
                [
                    'code' => 'moov',
                    'name' => 'Moov Money',
                    'color' => '#009FE3',
                    'available' => !empty(env('MOOV_API_KEY'))
                ],
                [
                    'code' => 'celtiis',
                    'name' => 'Celtiis Cash',
                    'color' => '#FF6B35',
                    'available' => false
                ]
            ]
        ]);
    }
}
