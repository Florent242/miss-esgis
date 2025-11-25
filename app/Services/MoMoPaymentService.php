<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoMoPaymentService
{
    private function getBaseUrl($operator)
    {
        $urls = [
            'mtn' => env('MTN_MOMO_ENVIRONMENT') === 'production' 
                ? 'https://proxy.momoapi.mtn.com' 
                : 'https://sandbox.momodeveloper.mtn.com',
            'moov' => 'https://api.moov-africa.bj',
        ];
        return $urls[$operator] ?? null;
    }

    private function getCurrency($operator, $environment)
    {
        // Pour MTN: EUR en sandbox, XOF en production
        if ($operator === 'mtn') {
            return $environment === 'production' ? 'XOF' : 'EUR';
        }
        // Autres opérateurs utilisent toujours XOF
        return 'XOF';
    }

    public function requestToPay($operator, $phoneNumber, $amount, $reference, $externalId = null)
    {
        $method = "requestToPay" . ucfirst($operator);
        
        if (method_exists($this, $method)) {
            return $this->$method($phoneNumber, $amount, $reference, $externalId);
        }
        
        return [
            'success' => false,
            'error' => 'Opérateur non supporté pour le débit direct'
        ];
    }

    private function requestToPayMtn($phoneNumber, $amount, $reference, $externalId)
    {
        try {
            $apiUser = env('MTN_MOMO_API_USER');
            $apiKey = env('MTN_MOMO_API_KEY');
            $subscriptionKey = env('MTN_MOMO_SUBSCRIPTION_KEY'); // Primary key
            
            if (!$apiUser || !$apiKey || !$subscriptionKey) {
                Log::error('MTN config missing', [
                    'has_user' => !empty($apiUser),
                    'has_key' => !empty($apiKey),
                    'has_subscription' => !empty($subscriptionKey)
                ]);
                return ['success' => false, 'error' => 'Configuration MTN manquante'];
            }

            $baseUrl = $this->getBaseUrl('mtn');
            $environment = env('MTN_MOMO_ENVIRONMENT', 'sandbox');
            
            // Étape 1: Générer le token OAuth avec Basic Auth
            $tokenResponse = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $subscriptionKey,
                'Content-Length' => '0',
            ])->withBasicAuth($apiUser, $apiKey)
              ->post($baseUrl . '/collection/token/');

            if (!$tokenResponse->successful()) {
                Log::error('MTN token error', [
                    'status' => $tokenResponse->status(),
                    'body' => $tokenResponse->body()
                ]);
                return ['success' => false, 'error' => 'Erreur authentification MTN'];
            }

            $tokenData = $tokenResponse->json();
            if (!isset($tokenData['access_token'])) {
                Log::error('MTN token missing', ['response' => $tokenData]);
                return ['success' => false, 'error' => 'Token non reçu'];
            }

            $token = $tokenData['access_token'];

            // Formater le numéro de téléphone
            $phone = preg_replace('/\D/', '', $phoneNumber);
            if (strpos($phone, '229') === 0) {
                $phone = substr($phone, 3); // Enlever le préfixe pays pour Bénin
            }

            // IMPORTANT: Le sandbox MTN utilise EUR, la production utilise XOF
            $currency = $environment === 'sandbox' ? 'EUR' : 'XOF';

            // Étape 2: Faire la requête de paiement
            $payload = [
                'amount' => (string) $amount,
                'currency' => $currency,
                'externalId' => $externalId ?? $reference,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $phone
                ],
                'payerMessage' => 'Vote Miss ESGIS',
                'payeeNote' => 'Vote ref: ' . $reference
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Reference-Id' => $reference,
                'X-Target-Environment' => $environment,
                'Ocp-Apim-Subscription-Key' => $subscriptionKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/collection/v1_0/requesttopay', $payload);

            $statusCode = $response->status();
            
            Log::info('MTN Payment attempt', [
                'status' => $statusCode,
                'body' => $response->body(),
                'payload' => $payload,
                'headers_sent' => [
                    'X-Reference-Id' => $reference,
                    'X-Target-Environment' => $environment
                ]
            ]);

            if ($statusCode === 202) {
                Log::info('MTN payment initiated successfully', [
                    'reference' => $reference,
                    'currency' => $currency
                ]);
                return [
                    'success' => true,
                    'reference' => $reference,
                    'status' => 'pending'
                ];
            }

            Log::error('MTN payment failed', [
                'status' => $statusCode,
                'body' => $response->body(),
                'json' => $response->json()
            ]);
            
            $errorMsg = $response->body() ?: ($response->json()['message'] ?? 'Unknown error');
            
            return [
                'success' => false, 
                'error' => 'Erreur MTN (' . $statusCode . '): ' . $errorMsg
            ];

        } catch (\Exception $e) {
            Log::error('MTN payment exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
        }
    }

    private function requestToPayMoov($phoneNumber, $amount, $reference, $externalId)
    {
        // Moov Africa API
        try {
            $apiKey = env('MOOV_API_KEY');
            
            if (!$apiKey) {
                return ['success' => false, 'error' => 'Configuration Moov manquante'];
            }

            $phone = preg_replace('/^229/', '', preg_replace('/\D/', '', $phoneNumber));

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrls['moov'] . '/v1/payments/debit', [
                'amount' => $amount,
                'currency' => 'XOF',
                'customer_phone' => $phone,
                'reference' => $reference,
                'description' => 'Vote Miss ESGIS'
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'reference' => $reference,
                    'transaction_id' => $response->json()['transaction_id'] ?? $reference
                ];
            }

            return ['success' => false, 'error' => 'Erreur Moov API'];

        } catch (\Exception $e) {
            Log::error('Moov payment exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function checkStatus($operator, $reference)
    {
        $method = "checkStatus" . ucfirst($operator);
        
        if (method_exists($this, $method)) {
            return $this->$method($reference);
        }
        
        return ['status' => 'unknown'];
    }

    private function checkStatusMtn($reference)
    {
        try {
            $apiUser = env('MTN_MOMO_API_USER');
            $apiKey = env('MTN_MOMO_API_KEY');
            $subscriptionKey = env('MTN_MOMO_SUBSCRIPTION_KEY');
            $baseUrl = $this->getBaseUrl('mtn');

            $tokenResponse = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $subscriptionKey,
            ])->withBasicAuth($apiUser, $apiKey)
              ->post($baseUrl . '/collection/token/');

            $token = $tokenResponse->json()['access_token'];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Target-Environment' => env('MTN_MOMO_ENVIRONMENT', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $subscriptionKey,
            ])->get($baseUrl . '/collection/v1_0/requesttopay/' . $reference);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => strtolower($data['status'] ?? 'unknown'),
                    'data' => $data
                ];
            }

            return ['status' => 'unknown'];

        } catch (\Exception $e) {
            Log::error('MTN status check error', ['error' => $e->getMessage()]);
            return ['status' => 'unknown'];
        }
    }
}
