<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSandbox;
use App\Models\Transaction;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmsWebhookController extends Controller
{
    public function receive(Request $request)
    {
        // Vérifier la clé API (sécurité)
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');
        
        if ($apiKey !== env('SMS_GATEWAY_API_KEY')) {
            Log::warning('SMS Webhook: Invalid API key', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $smsContent = $request->input('message') ?? $request->input('text') ?? '';
        $senderNumber = $request->input('from') ?? $request->input('sender') ?? '';
        
        Log::info('SMS received', [
            'from' => $senderNumber,
            'message' => $smsContent,
            'full_request' => $request->all()
        ]);

        // Parser le SMS pour extraire les informations de paiement
        $paymentInfo = $this->parseMoMoSms($smsContent, $senderNumber);

        if (!$paymentInfo) {
            Log::warning('SMS non reconnu comme paiement MoMo', ['sms' => $smsContent]);
            return response()->json(['message' => 'SMS not recognized as payment'], 200);
        }

        // Trouver la transaction en attente correspondante
        $payment = $this->findMatchingPayment($paymentInfo);

        if (!$payment) {
            Log::info('Aucune transaction en attente trouvée', $paymentInfo);
            return response()->json(['message' => 'No matching pending payment'], 200);
        }

        // Valider le paiement
        $this->confirmPayment($payment, $smsContent);

        return response()->json(['message' => 'Payment processed successfully'], 200);
    }

    private function parseMoMoSms($sms, $sender)
    {
        // Patterns pour différents opérateurs
        $patterns = [
            // MTN : "Vous avez recu 500 FCFA de 91234567. Ref: ABC123. Solde: 1000 FCFA"
            'mtn' => '/(?:recu|reçu)\s+(\d+)\s*(?:F|FCFA|CFA).*?(?:de|from)\s*(\d{8,})/i',
            
            // Moov : "Reception de 500 FCFA. Expediteur: 97234567. Ref: XYZ789"
            'moov' => '/(?:reception|recept)\s+.*?(\d+)\s*(?:F|FCFA|CFA).*?(?:expediteur|emetteur)\s*:?\s*(\d{8,})/i',
            
            // Générique
            'generic' => '/(\d+)\s*(?:F|FCFA|CFA).*?(\d{8,})/i',
        ];

        foreach ($patterns as $operator => $pattern) {
            if (preg_match($pattern, $sms, $matches)) {
                return [
                    'amount' => (float) $matches[1],
                    'sender_phone' => preg_replace('/\D/', '', $matches[2] ?? ''),
                    'operator' => $this->detectOperator($sender, $sms)
                ];
            }
        }

        return null;
    }

    private function detectOperator($phoneNumber, $sms)
    {
        // Détecter l'opérateur basé sur le numéro ou le contenu du SMS
        $phone = preg_replace('/\D/', '', $phoneNumber);
        
        if (str_starts_with($phone, '229') || str_starts_with($phone, '91') || str_starts_with($phone, '90') || str_starts_with($phone, '96') || str_starts_with($phone, '97')) {
            if (stripos($sms, 'mtn') !== false || str_starts_with($phone, '229') && in_array(substr($phone, 3, 2), ['90', '91', '96', '97'])) {
                return 'mtn';
            }
        }
        
        if (stripos($sms, 'moov') !== false || str_starts_with($phone, '229') && in_array(substr($phone, 3, 2), ['94', '95', '98', '99'])) {
            return 'moov';
        }
        
        return 'mtn'; // Par défaut
    }

    private function findMatchingPayment($paymentInfo)
    {
        // Chercher une transaction en attente qui correspond
        $payment = PaymentSandbox::where('status', 'pending')
            ->where('amount', $paymentInfo['amount'])
            ->where('phone_number', 'LIKE', '%' . substr($paymentInfo['sender_phone'], -8))
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        return $payment;
    }

    private function confirmPayment($payment, $smsContent)
    {
        try {
            DB::beginTransaction();

            // Mettre à jour le paiement sandbox
            $payment->status = 'confirmed';
            $payment->sms_content = $smsContent;
            $payment->sms_received_at = now();
            $payment->save();

            // Créer la transaction officielle
            $transaction = Transaction::create([
                'reference' => $payment->reference,
                'miss_id' => $payment->miss_id,
                'montant' => $payment->amount,
                'numero_telephone' => $payment->phone_number,
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

            Log::info('Payment confirmed via SMS', [
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
}
