<?php

echo "🧪 TEST LIVE FEDAPAY - Numéro 61804972\n";
echo str_repeat("=", 70) . "\n\n";

// Configuration
$apiUrl = 'https://monea-pay.loca.lt/api';
$apiKey = 'fedapay_api_key_123456789';
$phoneNumber = '+22961804972'; // Votre numéro

// Test 1: Créer une transaction de test
echo "📱 Étape 1: Création de transaction FedaPay\n";
echo str_repeat("-", 70) . "\n";

$transactionData = [
    'description' => 'Test Vote Miss ESGIS - LIVE',
    'amount' => 100, // 100 XOF = 1 vote
    'currency' => 'XOF',
    'customer' => [
        'firstname' => 'Test',
        'lastname' => 'Live',
        'email' => 'test.live@miss-esgis.com',
        'phone' => $phoneNumber,
        'country' => 'bj'
    ]
];

$ch = curl_init($apiUrl . '/transactions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-API-Key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transactionData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($httpCode === 200 || $httpCode === 201) {
    echo "✅ Transaction créée avec succès!\n";
    echo "   ID Local: " . ($result['data']['id'] ?? 'N/A') . "\n";
    echo "   FedaPay ID: " . ($result['data']['fedapay_id'] ?? 'N/A') . "\n";
    echo "   Statut: " . ($result['data']['status'] ?? 'N/A') . "\n";
    echo "   Montant: " . ($result['data']['amount'] ?? 'N/A') . " XOF\n";
    echo "   Téléphone: " . ($result['data']['customer']['phone'] ?? 'N/A') . "\n";
    echo "\n";
    echo "   🌐 URL de paiement:\n";
    echo "   " . ($result['data']['payment_url'] ?? 'N/A') . "\n";
    
    $fedapayId = $result['data']['fedapay_id'] ?? null;
    $paymentUrl = $result['data']['payment_url'] ?? null;
} else {
    echo "❌ Erreur lors de la création: HTTP $httpCode\n";
    echo "   Réponse: $response\n";
    exit(1);
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "�� Étape 2: Vérification de la transaction\n";
echo str_repeat("-", 70) . "\n";

if (isset($fedapayId)) {
    sleep(2); // Attendre un peu
    
    $ch = curl_init($apiUrl . '/transactions/' . $fedapayId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-Key: ' . $apiKey
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✅ Transaction récupérée\n";
        echo "   Statut: " . ($data['data']['status'] ?? 'N/A') . "\n";
        echo "   Montant: " . ($data['data']['amount'] ?? 'N/A') . " XOF\n";
    } else {
        echo "⚠️  Impossible de récupérer la transaction\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "🎯 Instructions de Test\n";
echo str_repeat("-", 70) . "\n";
echo "1. Ouvrez cette URL dans votre navigateur:\n";
echo "   $paymentUrl\n\n";
echo "2. Choisissez votre méthode de paiement (MTN/Moov)\n";
echo "3. Entrez votre code PIN Mobile Money\n";
echo "4. Validez le paiement de 100 XOF\n\n";
echo "5. Le webhook sera automatiquement appelé et les votes créés\n\n";

echo "📊 Surveiller les logs:\n";
echo "   tail -f /home/admin/monea-pay/api/logs/webhook.log\n";
echo "   tail -f /home/admin/monea-pay/api/logs/payments.log\n\n";

echo "✅ Test live prêt! Utilisez le numéro +229 61804972\n";
