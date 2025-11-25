<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoMoPaymentService
{
    private function getBaseUrl($operator)
    {
        $urls = [
            'mtn' => env('MTN_MOMO_ENVIRONMENT') === 'sandbox' 
                ? 'https://sandbox.momodeveloper.mtn.com' 
                : 'https://proxy.momoapi.mtn.com',
            'moov' => 'https://api.moov-africa.bj',
        ];
        return $urls[$operator] ?? null;
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
            $subscriptionKey = env('MTN_MOMO_SUBSCRIPTION_KEY');
            
            if (!$apiUser || !$apiKey || !$subscriptionKey) {
                return ['success' => false, 'error' => 'Configuration MTN manquante'];
            }

            $baseUrl = $this->getBaseUrl('mtn');
            
            // Générer le token OAuth
            $tokenResponse = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $subscriptionKey,
            ])->withBasicAuth($apiUser, $apiKey)
              ->post($baseUrl . '/collection/token/');

            if (!$tokenResponse->successful()) {
                Log::error('MTN token error', ['response' => $tokenResponse->body()]);
                return ['success' => false, 'error' => 'Erreur d\'authentification MTN'];
            }

            $token = $tokenResponse->json()['access_token'];

            // Formater le numéro (sans préfixe pays pour le sandbox)
            $phone = preg_replace('/\D/', '', $phoneNumber);

            // MTN API gère automatiquement le pop-up USSD sur le téléphone du client
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Reference-Id' => $reference,
                'X-Target-Environment' => env('MTN_MOMO_ENVIRONMENT', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $subscriptionKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/collection/v1_0/requesttopay', [
                'amount' => (string) $amount,
                'currency' => 'XOF',
                'externalId' => $externalId ?? $reference,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $phone
                ],
                'payerMessage' => 'Vote Miss ESGIS',
                'payeeNote' => 'Vote candidate'
            ]);

            if ($response->status() === 202) {
                Log::info('MTN payment initiated', ['reference' => $reference]);
                return [
                    'success' => true,
                    'reference' => $reference,
                    'status' => 'pending'
                ];
            }

            Log::error('MTN payment error', ['response' => $response->body()]);
            return ['success' => false, 'error' => 'Erreur lors de l\'initiation du paiement'];

        } catch (\Exception $e) {
            Log::error('MTN payment exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
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
