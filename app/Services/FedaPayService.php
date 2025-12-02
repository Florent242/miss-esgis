<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FedaPayService
{
    private $apiUrl = 'https://pay.aiko.qzz.io/tower-send-dev/api';
    private $apiKey = 'fedapay_api_key_123456789';

    /**
     * Créer une transaction de paiement via l'API cURL
     */
    public function createTransaction($amount, $description, $customerId = null, $metadata = [])
    {
        try {
            $payload = [
                'description' => $description,
                'amount' => (int)$amount,
                'currency' => 'XOF',
                'customer' => [
                    'firstname' => $metadata['firstname'] ?? 'Voteur',
                    'lastname' => $metadata['lastname'] ?? 'Miss ESGIS',
                    'email' => $metadata['email'] ?? 'vote@reine-esgis.com',
                    'phone' => $metadata['phone'] ?? '+22966000001',
                    'country' => 'bj'
                ],
                'webhook_url' => route('fedapay.webhook')
            ];

            Log::info('Sending request to external FedaPay API', [
                'url' => $this->apiUrl . '/transactions',
                'amount' => $amount,
                'description' => $description
            ]);

            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->apiKey
            ])->post("{$this->apiUrl}/transactions", $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['success']) && $result['success'] && isset($result['data']['fedapay_id'])) {
                    Log::info('FedaPay transaction created successfully via external API', [
                        'local_id' => $result['data']['id'] ?? null,
                        'fedapay_id' => $result['data']['fedapay_id'],
                        'amount' => $amount,
                        'status' => $result['data']['status'] ?? 'pending'
                    ]);

                    return [
                        'success' => true,
                        'transaction_id' => $result['data']['fedapay_id'],
                        'payment_url' => $result['data']['payment_url'],
                        'reference' => $result['data']['id']
                    ];
                } else {
                    Log::error('External API returned invalid response structure', [
                        'response_body' => $result,
                        'amount' => $amount
                    ]);
                    
                    return [
                        'success' => false,
                        'error' => 'Format de réponse invalide de l\'API externe'
                    ];
                }
            }

            Log::error('FedaPay API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
                'request_data' => [
                    'description' => $description,
                    'amount' => (int)$amount,
                    'metadata' => $metadata
                ]
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la création de la transaction'
            ];

        } catch (\Exception $e) {
            Log::error('FedaPay cURL exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $this->apiUrl,
                'api_key' => substr($this->apiKey, 0, 10) . '...'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Récupérer le statut d'une transaction
     */
    public function getTransactionStatus($transactionId)
    {
        try {
            // Essayer d'abord avec le vrai SDK FedaPay
            try {
                \FedaPay\FedaPay::setApiKey(config('services.fedapay.secret_key') ?? 'sk_live_R90vA_Z7ZALSryZh2iY_MbbC');
                \FedaPay\FedaPay::setEnvironment(config('services.fedapay.environment') ?? 'live');
                
                $transaction = \FedaPay\Transaction::retrieve($transactionId);
                
                Log::info('FedaPay status checked via SDK', [
                    'transaction_id' => $transactionId,
                    'status' => $transaction->status,
                    'reference' => $transaction->reference ?? null
                ]);

                return [
                    'success' => true,
                    'status' => $transaction->status,
                    'amount' => $transaction->amount,
                    'reference' => $transaction->reference ?? null,
                    'fedapay_id' => $transaction->id
                ];
                
            } catch (\Exception $sdkError) {
                // Si le SDK échoue, essayer l'API cURL de backup
                Log::info('SDK failed, trying cURL API', ['error' => $sdkError->getMessage()]);
                
                $response = Http::withHeaders([
                    'X-API-Key' => $this->apiKey
                ])->get("{$this->apiUrl}/transactions/{$transactionId}");

                if ($response->successful()) {
                    $result = $response->json();
                    
                    Log::info('FedaPay status checked via cURL', [
                        'transaction_id' => $transactionId,
                        'status' => $result['data']['status'] ?? 'unknown'
                    ]);

                    return [
                        'success' => true,
                        'status' => $result['data']['status'],
                        'amount' => $result['data']['amount'],
                        'reference' => $result['data']['reference'] ?? null,
                        'fedapay_id' => $result['data']['fedapay_id'] ?? $transactionId
                    ];
                }
            }

            Log::warning('FedaPay status check failed (both methods)', [
                'transaction_id' => $transactionId
            ]);

            return [
                'success' => false,
                'error' => 'Transaction non trouvée'
            ];

        } catch (\Exception $e) {
            Log::error('FedaPay status check error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculer le nombre de votes (100 XOF = 1 vote minimum)
     */
    public function calculateVotes($amount)
    {
        return intval($amount / 100);
    }

    /**
     * Tester la connexion à l'API externe
     */
    public function testExternalApi()
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'X-API-Key' => $this->apiKey
            ])->get("{$this->apiUrl}/");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => 'success',
                    'api_name' => $data['name'] ?? 'Unknown',
                    'version' => $data['version'] ?? 'Unknown',
                    'response_time' => $response->transferStats?->getTransferTime() ?? null
                ];
            }

            return [
                'status' => 'error',
                'message' => 'API externe non accessible',
                'http_code' => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier la signature webhook (optionnel pour l'instant)
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        // Pour l'instant, on accepte tous les webhooks de notre propre API
        return true;
    }
}
