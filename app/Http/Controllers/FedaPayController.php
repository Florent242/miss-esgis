<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FedaPayService;
use App\Models\Transaction;
use App\Models\Vote;
use App\Models\Miss;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FedaPayController extends Controller
{
    protected $fedaPayService;
    public function __construct(FedaPayService $fedaPayService)
    {
        $this->fedaPayService = $fedaPayService;
    }

    /**
     * Créer une transaction (format cURL API)
     */
    public function createTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:100',
                'customer' => 'required|array',
                'customer.firstname' => 'required|string',
                'customer.lastname' => 'required|string',
                'customer.email' => 'required|email',
                'customer.phone' => 'required|string',
            ]);

            // Générer une référence unique
            $reference = 'FEDAPAY-' . strtoupper(Str::random(10));

            // Métadonnées
            $metadata = [
                'reference' => $reference,
                'phone' => $validated['customer']['phone'],
                'email' => $validated['customer']['email'],
            ];

            // Créer la transaction FedaPay
            $result = $this->fedaPayService->createTransaction(
                $validated['amount'],
                $validated['description'],
                null,
                $metadata
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Erreur lors de la création de la transaction'
                ], 400);
            }

            Log::info('FedaPay transaction created via cURL API', [
                'reference' => $reference,
                'fedapay_id' => $result['transaction_id'],
                'amount' => $validated['amount']
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $reference,
                    'fedapay_id' => $result['transaction_id'],
                    'description' => $validated['description'],
                    'amount' => $validated['amount'],
                    'currency' => 'XOF',
                    'status' => 'pending',
                    'payment_url' => $result['payment_url'],
                    'customer' => $validated['customer'],
                    'created_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('FedaPay transaction creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initier un paiement
     */
    public function initiate(Request $request)
    {
        try {
            $validated = $request->validate([
                'miss_id' => 'required|exists:misses,id',
                'phone_number' => 'required|string|min:8|max:20|regex:/^\+229[0-9]{8,10}$/',
                'amount' => 'required|numeric|min:100|max:500000',
                'vote_count' => 'required|integer|min:1|max:5000',
                'email' => 'nullable|email|max:255',
            ]);
            
            Log::info('FedaPay initiate request successful', [
                'miss_id' => $validated['miss_id'],
                'amount' => $validated['amount'],
                'vote_count' => $validated['vote_count'],
                'phone' => substr($validated['phone_number'], 0, 8) . 'xxx'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('FedaPay initiate validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['email', 'phone_number']),
                'url' => $request->url()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Données invalides',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('FedaPay initiate general error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur lors de l\'initialisation du paiement'
            ], 500);
        }

        $miss = Miss::findOrFail($validated['miss_id']);

        if ($miss->statut !== 'active') {
            return response()->json(['error' => 'Cette candidate ne peut pas recevoir de votes'], 400);
        }

        // Générer une référence unique
        $reference = 'VOTE-' . strtoupper(Str::random(10));

        // Métadonnées optimisées pour l'API externe
        $metadata = [
            'reference' => $reference,
            'miss_id' => $validated['miss_id'],
            'miss_name' => $miss->nom . ' ' . $miss->prenom,
            'vote_count' => $validated['vote_count'],
            'phone' => $validated['phone_number'],
            'email' => $validated['email'] ?? 'vote@reine-esgis.com',
            'firstname' => 'Voteur',
            'lastname' => 'Miss ESGIS ' . $miss->prenom,
        ];

        // Créer la transaction FedaPay
        $result = $this->fedaPayService->createTransaction(
            $validated['amount'],
            "Vote pour {$miss->nom} {$miss->prenom} - {$validated['vote_count']} vote(s)",
            null,
            $metadata
        );

        if (!$result['success']) {
            Log::warning('FedaPay transaction creation failed', [
                'reference' => $reference,
                'miss_id' => $validated['miss_id'],
                'amount' => $validated['amount'],
                'error' => $result['error'] ?? 'Unknown error'
            ]);
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Erreur lors de l\'initialisation du paiement'
            ], 400);
        }

        // Créer la transaction en base de données (statut pending)
        $transaction = Transaction::create([
            'reference' => $reference,
            'miss_id' => $validated['miss_id'],
            'montant' => $validated['amount'],
            'numero_telephone' => $validated['phone_number'],
            'methode' => 'fedapay',
            'statut' => 'pending',
            'transaction_id' => $result['transaction_id'], // Sauvegarder le fedapay_id
        ]);

        // Sauvegarder la référence en session pour vérification ultérieure
        session(['vote_reference' => $reference]);

        Log::info('FedaPay payment initiated', [
            'reference' => $reference,
            'fedapay_id' => $result['transaction_id'],
            'amount' => $validated['amount']
        ]);

        return response()->json([
            'success' => true,
            'reference' => $reference,
            'payment_url' => $result['payment_url'],
            'transaction_id' => $result['transaction_id'],
            'message' => 'Redirigez le client vers payment_url'
        ]);
    }

    /**
     * Webhook depuis l'API monea-pay.loca.lt
     */
    public function webhook(Request $request)
    {
        // Vérifier si c'est un appel GET avec paramètres (comme callback)
        if ($request->isMethod('get') && $request->has('status')) {
            return $this->callback($request);
        }

        $data = $request->all();
        
        Log::info('FedaPay webhook received from monea-pay.loca.lt', ['data' => $data]);

        // Structure du webhook de l'API cURL
        $entity = $data['entity'] ?? null;
        $transaction = $entity['transaction'] ?? null;

        if (!$transaction) {
            Log::error('FedaPay webhook: invalid data structure');
            return response()->json(['error' => 'Invalid webhook data'], 400);
        }

        $fedapayId = $transaction['id'];
        $status = $transaction['status'];
        $reference = $transaction['reference'] ?? null;

        Log::info('Processing FedaPay webhook', [
            'fedapay_id' => $fedapayId,
            'status' => $status,
            'reference' => $reference
        ]);

        // Traiter selon le statut
        if ($status === 'approved') {
            $this->processApprovedPayment($fedapayId, $transaction);
        } elseif ($status === 'declined') {
            $this->processDeclinedPayment($fedapayId);
        } elseif ($status === 'canceled') {
            $this->processCanceledPayment($fedapayId);
        }

        return response()->json(['success' => true, 'message' => 'Webhook processed'], 200);
    }

    /**
     * Traiter un paiement approuvé
     */
    private function processApprovedPayment($fedapayId, $transactionData)
    {
        try {
            $customer = $transactionData['customer'] ?? [];
            $customerEmail = $customer['email'] ?? null;

            // Chercher la transaction par fedapay_id (transaction_id en BDD)
            $transaction = Transaction::where('transaction_id', $fedapayId)
                ->orWhere('reference', 'FEDAPAY-' . $fedapayId)
                ->first();

            if (!$transaction) {
                Log::error('FedaPay: Transaction not found in database', [
                    'fedapay_id' => $fedapayId,
                    'customer_email' => $customerEmail
                ]);
                return;
            }

            if ($transaction->statut === 'completed') {
                Log::info('FedaPay: Transaction already completed', [
                    'reference' => $transaction->reference
                ]);
                return;
            }

            DB::beginTransaction();

            // Mettre à jour la transaction
            $transaction->statut = 'completed';
            $transaction->save();

            // Calculer le nombre de votes (100 FCFA = 1 vote)
            $voteCount = intval($transaction->montant / 100);
            $missId = $transaction->miss_id;
            
            // Utiliser le service de distribution intelligente
            $this->voteDistributionService->distributeVotes($missId, $transaction->id, $voteCount);

            DB::commit();

            Log::info('FedaPay payment processed successfully', [
                'reference' => $transaction->reference,
                'fedapay_id' => $fedapayId,
                'votes_created' => $voteCount,
                'miss_id' => $missId,
                'amount' => $transaction->montant
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FedaPay payment processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Traiter un paiement refusé
     */
    private function processDeclinedPayment($fedapayId)
    {
        $transaction = Transaction::where('transaction_id', $fedapayId)->first();
        
        if ($transaction) {
            $transaction->statut = 'failed';
            $transaction->save();
            
            Log::info('FedaPay payment declined', [
                'fedapay_id' => $fedapayId,
                'reference' => $transaction->reference
            ]);
        }
    }

    /**
     * Traiter un paiement annulé
     */
    private function processCanceledPayment($fedapayId)
    {
        $transaction = Transaction::where('transaction_id', $fedapayId)->first();
        
        if ($transaction) {
            $transaction->statut = 'canceled';
            $transaction->save();
            
            Log::info('FedaPay payment canceled', [
                'fedapay_id' => $fedapayId,
                'reference' => $transaction->reference
            ]);
        }
    }

    /**
     * Callback depuis FedaPay (retour utilisateur)
     */
    public function callback(Request $request)
    {
        $status = $request->query('status');
        $close = $request->query('close');
        $fedapayId = $request->query('id');
        
        Log::info('FedaPay callback', [
            'status' => $status,
            'close' => $close,
            'id' => $fedapayId
        ]);

        // Si annulé ou fermé (sauf si approved)
        if ($close === 'true' && $status !== 'approved') {
            return redirect('/')->with('info', 'Paiement annulé');
        }

        if ($status === 'canceled') {
            return redirect('/')->with('error', 'Paiement annulé');
        }

        // Si paiement approuvé, vérifier et traiter
        if ($status === 'approved' && $fedapayId) {
            // Chercher la transaction locale
            $transaction = Transaction::where('reference', 'LIKE', 'VOTE-%')
                ->where('statut', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$transaction || !$transaction->miss_id) {
                return redirect('/')->with('error', 'Transaction introuvable');
            }

            // Vérifier le statut avec retry (max 7 tentatives)
            $realStatus = null;
            $maxRetries = 7;
            $retryDelay = 3; // secondes

            for ($i = 0; $i < $maxRetries; $i++) {
                Log::info("FedaPay status check attempt " . ($i + 1) . "/{$maxRetries}", ['fedapay_id' => $fedapayId]);
                
                $apiStatus = $this->fedaPayService->getTransactionStatus($fedapayId);
                
                if ($apiStatus['success']) {
                    $realStatus = $apiStatus['status'];
                    Log::info("FedaPay real status found", ['status' => $realStatus, 'attempt' => ($i + 1)]);
                    break;
                }
                
                // Si pas réussi et pas dernière tentative, attendre
                if ($i < $maxRetries - 1) {
                    Log::info("Waiting {$retryDelay}s before retry...");
                    sleep($retryDelay);
                }
            }

            // Si le statut est vraiment approved
            if ($realStatus === 'approved') {
                try {
                    DB::beginTransaction();

                    // Vérifier si déjà traité
                    if ($transaction->statut === 'completed') {
                        DB::rollBack();
                        return redirect('/vote/' . $transaction->miss_id)
                            ->with('info', 'Ce paiement a déjà été traité');
                    }

                    // Mettre à jour la transaction
                    $transaction->statut = 'completed';
                    $transaction->save();

                    // Calculer le nombre de votes (100 FCFA = 1 vote)
                    $voteCount = intval($transaction->montant / 100);
                    
                    // Utiliser le service de distribution intelligente
                    $this->voteDistributionService->distributeVotes($transaction->miss_id, $transaction->id, $voteCount);

                    DB::commit();

                    Log::info('FedaPay payment processed via callback', [
                        'reference' => $transaction->reference,
                        'fedapay_id' => $fedapayId,
                        'votes_created' => $voteCount,
                        'miss_id' => $transaction->miss_id
                    ]);

                    // Rediriger vers la page de succès
                    return redirect('/vote/' . $transaction->miss_id)
                        ->with('success', '🎉 Paiement confirmé ! ' . $voteCount . ' vote(s) enregistré(s) avec succès.');

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('FedaPay callback payment processing error', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return redirect('/vote/' . $transaction->miss_id)
                        ->with('error', 'Erreur lors de l\'enregistrement des votes');
                }
            } else {
                // Statut non confirmé, rediriger avec message d'attente
                Log::warning('FedaPay payment not confirmed', [
                    'fedapay_id' => $fedapayId,
                    'real_status' => $realStatus
                ]);
                
                return redirect('/vote/' . $transaction->miss_id)
                    ->with('warning', 'Paiement en cours de vérification. Veuillez patienter...');
            }
        }

        // Si en attente (pending)
        if ($status === 'pending' && $fedapayId) {
            $transaction = Transaction::where('reference', 'LIKE', 'VOTE-%')
                ->where('statut', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($transaction && $transaction->miss_id) {
                return redirect('/vote/' . $transaction->miss_id)
                    ->with('info', '⏳ Paiement en cours de traitement. Vous recevrez une confirmation sous peu.');
            }
        }

        // Par défaut, rediriger vers l'accueil
        return redirect('/')->with('info', 'Retour du paiement');
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string'
        ]);

        $transaction = Transaction::where('reference', $validated['reference'])->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction non trouvée'], 404);
        }

        // Si en attente, vérifier sur FedaPay
        if ($transaction->statut === 'pending' && $transaction->transaction_id) {
            $result = $this->fedaPayService->getTransactionStatus($transaction->transaction_id);
            
            if ($result['success'] && $result['status'] === 'approved') {
                // Traiter le paiement
                $this->processApprovedPayment($result);
                $transaction->refresh();
            }
        }

        return response()->json([
            'status' => $transaction->statut,
            'reference' => $transaction->reference,
        ]);
    }

    /**
     * Test de connexion à l'API externe (diagnostic)
     */
    public function testConnection(Request $request)
    {
        try {
            // Test de l'API externe
            $externalTest = $this->fedaPayService->testExternalApi();
            
            // Récupérer les dernières transactions
            $recentTransactions = Transaction::where('methode', 'fedapay')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'reference', 'montant', 'statut', 'created_at']);

            return response()->json([
                'success' => true,
                'external_api' => $externalTest,
                'recent_transactions' => $recentTransactions,
                'system_status' => [
                    'laravel_version' => app()->version(),
                    'php_version' => PHP_VERSION,
                    'environment' => config('app.env'),
                    'fedapay_configured' => !empty(config('services.fedapay.secret_key'))
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('FedaPay test connection error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
