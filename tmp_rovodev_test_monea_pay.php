<?php
/**
 * Test simple de connexion à monea-pay.loca.lt
 */

echo "🧪 Test de connexion à monea-pay.loca.lt\n";
echo str_repeat("=", 60) . "\n\n";

// Configuration
$apiUrl = 'https://pay.aiko.qzz.io/tower-send-dev/api';
$apiKey = 'fedapay_api_key_123456789';

// Test 1: Ping de l'API
echo "📡 Test 1: Vérification de l'accessibilité de l'API\n";
echo "URL: {$apiUrl}\n";
echo str_repeat("-", 60) . "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/ping');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-API-Key: ' . $apiKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Erreur cURL: {$error}\n";
} else {
    echo "📊 Code HTTP: {$httpCode}\n";
    echo "📄 Réponse: " . ($response ?: 'Vide') . "\n";
}

echo "\n";

// Test 2: Tentative de création de transaction
echo "💳 Test 2: Création de transaction test\n";
echo str_repeat("-", 60) . "\n";

$transactionData = [
    'description' => 'Test Vote Miss ESGIS - Nouvelle URL',
    'amount' => 500,
    'currency' => 'XOF',
    'customer' => [
        'firstname' => 'Test',
        'lastname' => 'User',
        'email' => 'test@test.com',
        'phone' => '+22966000001',
        'country' => 'bj'
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . '/transactions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transactionData));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-API-Key: ' . $apiKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Erreur cURL: {$error}\n";
} else {
    echo "📊 Code HTTP: {$httpCode}\n";
    echo "📄 Réponse: " . ($response ?: 'Vide') . "\n";
    
    if ($httpCode == 200 && $response) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "✅ Transaction créée avec succès!\n";
            if (isset($data['data']['fedapay_id'])) {
                echo "🆔 ID FedaPay: {$data['data']['fedapay_id']}\n";
            }
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🏁 Fin des tests\n";
?>
