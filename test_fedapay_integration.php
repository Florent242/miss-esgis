<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

echo "🧪 Test FedaPay Integration\n";
echo str_repeat("=", 60) . "\n\n";

// Test 1: Créer une transaction via monea-pay.loca.lt
echo "📱 Test 1: Création de transaction FedaPay\n";
echo str_repeat("-", 60) . "\n";

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
    'X-API-Key' => 'fedapay_api_key_123456789'
])->post('https://monea-pay.loca.lt/api/transactions', [
    'description' => 'Test Vote Miss ESGIS',
    'amount' => 500,
    'currency' => 'XOF',
    'customer' => [
        'firstname' => 'Test',
        'lastname' => 'Integration',
        'email' => 'test.integration@miss-esgis.com',
        'phone' => '+22966000001',
        'country' => 'bj'
    ]
]);

if ($response->successful()) {
    $data = $response->json();
    echo "✅ Transaction créée avec succès!\n";
    echo "ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
    echo "FedaPay ID: " . ($data['data']['fedapay_id'] ?? 'N/A') . "\n";
    echo "Statut: " . ($data['data']['status'] ?? 'N/A') . "\n";
    echo "URL Paiement: " . ($data['data']['payment_url'] ?? 'N/A') . "\n";
    
    $fedapayId = $data['data']['fedapay_id'] ?? null;
} else {
    echo "❌ Erreur: " . $response->status() . "\n";
    echo $response->body() . "\n";
}

echo "\n";

// Test 2: Récupérer la transaction
if (isset($fedapayId)) {
    echo "📊 Test 2: Récupération de transaction\n";
    echo str_repeat("-", 60) . "\n";
    
    $response = Http::withHeaders([
        'X-API-Key' => 'fedapay_api_key_123456789'
    ])->get("https://monea-pay.loca.lt/api/transactions/{$fedapayId}");
    
    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Transaction récupérée\n";
        echo "Statut: " . ($data['data']['status'] ?? 'N/A') . "\n";
        echo "Montant: " . ($data['data']['amount'] ?? 'N/A') . " XOF\n";
    } else {
        echo "❌ Erreur récupération\n";
    }
}

echo "\n\n✅ Tests terminés!\n";
