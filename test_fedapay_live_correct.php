<?php

echo "🧪 TEST LIVE FEDAPAY - Numéro 01 61 80 49 72\n";
echo str_repeat("=", 70) . "\n\n";

// Configuration
$apiUrl = 'https://monea-pay.loca.lt/api';
$apiKey = 'fedapay_api_key_123456789';
$phoneNumber = '+2290161804972'; // Format correct: 01 au lieu de 61

// Test 1: Créer une transaction de test
echo "📱 Étape 1: Création de transaction FedaPay LIVE\n";
echo str_repeat("-", 70) . "\n";

$transactionData = [
    'description' => 'Vote Miss ESGIS - Test LIVE',
    'amount' => 100, // 100 XOF = 1 vote
    'currency' => 'XOF',
    'customer' => [
        'firstname' => 'Client',
        'lastname' => 'Test',
        'email' => 'client.test@miss-esgis.com',
        'phone' => $phoneNumber,
        'country' => 'bj'
    ]
];

echo "💳 Données de paiement:\n";
echo "   Téléphone: $phoneNumber\n";
echo "   Montant: 100 XOF (1 vote)\n";
echo "   Méthode: Mobile Money (MTN/Moov)\n\n";

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
    echo "✅ TRANSACTION CRÉÉE AVEC SUCCÈS!\n\n";
    echo "┌─────────────────────────────────────────────────────────┐\n";
    echo "│ DÉTAILS DE LA TRANSACTION                               │\n";
    echo "├─────────────────────────────────────────────────────────┤\n";
    echo "│ ID Local       : " . str_pad($result['data']['id'] ?? 'N/A', 39) . "│\n";
    echo "│ FedaPay ID     : " . str_pad($result['data']['fedapay_id'] ?? 'N/A', 39) . "│\n";
    echo "│ Statut         : " . str_pad($result['data']['status'] ?? 'N/A', 39) . "│\n";
    echo "│ Montant        : " . str_pad(($result['data']['amount'] ?? 'N/A') . ' XOF', 39) . "│\n";
    echo "│ Téléphone      : " . str_pad($result['data']['customer']['phone'] ?? 'N/A', 39) . "│\n";
    echo "└─────────────────────────────────────────────────────────┘\n\n";
    
    $fedapayId = $result['data']['fedapay_id'] ?? null;
    $paymentUrl = $result['data']['payment_url'] ?? null;
    
    echo "🌐 URL DE PAIEMENT:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "$paymentUrl\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
} else {
    echo "❌ ERREUR lors de la création: HTTP $httpCode\n";
    echo "   Réponse: $response\n";
    exit(1);
}

echo str_repeat("=", 70) . "\n";
echo "🎯 INSTRUCTIONS POUR EFFECTUER LE PAIEMENT\n";
echo str_repeat("=", 70) . "\n\n";

echo "1️⃣  OUVRIR L'URL DE PAIEMENT\n";
echo "   Copiez l'URL ci-dessus dans votre navigateur\n\n";

echo "2️⃣  CHOISIR LA MÉTHODE DE PAIEMENT\n";
echo "   ✓ MTN Mobile Money\n";
echo "   ✓ Moov Money\n";
echo "   ✓ Carte Visa/Mastercard\n\n";

echo "3️⃣  ENTRER VOS INFORMATIONS\n";
echo "   Numéro: 01 61 80 49 72\n";
echo "   Montant: 100 XOF\n\n";

echo "4️⃣  VALIDER LE PAIEMENT\n";
echo "   Entrez votre code PIN Mobile Money\n\n";

echo "5️⃣  CONFIRMATION AUTOMATIQUE\n";
echo "   Le webhook sera appelé automatiquement\n";
echo "   Le vote sera créé dans la base de données\n\n";

echo str_repeat("=", 70) . "\n";
echo "�� SURVEILLER L'ÉTAT DU PAIEMENT\n";
echo str_repeat("=", 70) . "\n\n";

echo "Logs de l'API Pay:\n";
echo "  tail -f /home/admin/monea-pay/api/logs/webhook.log\n";
echo "  tail -f /home/admin/monea-pay/api/logs/payments.log\n\n";

echo "Monitoring complet:\n";
echo "  php /home/admin/monea-pay/api/monitor.php\n\n";

if (isset($fedapayId)) {
    echo "Vérifier le statut:\n";
    echo "  curl -H 'X-API-Key: $apiKey' \\\n";
    echo "    $apiUrl/transactions/$fedapayId\n\n";
}

echo str_repeat("=", 70) . "\n";
echo "✅ TRANSACTION PRÊTE POUR LE PAIEMENT!\n";
echo "   ID FedaPay: $fedapayId\n";
echo "   Numéro: +229 01 61 80 49 72\n";
echo "   Montant: 100 XOF (1 vote)\n";
echo str_repeat("=", 70) . "\n";
