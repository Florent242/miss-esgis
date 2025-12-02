<?php

echo "💰 TEST PAIEMENT LIVE FEDAPAY - 100 XOF RÉELS\n";
echo str_repeat("=", 70) . "\n";
echo "⚠️  ATTENTION: Ce test va créer une vraie transaction de 100 XOF\n";
echo str_repeat("=", 70) . "\n\n";

// Configuration
$apiUrl = 'https://monea-pay.loca.lt/api';
$apiKey = 'fedapay_api_key_123456789';
$phoneNumber = '+2290161804972'; // Votre numéro LIVE

echo "📋 Informations de test:\n";
echo "   API: $apiUrl\n";
echo "   Mode: LIVE (sk_live_R90vA_Z7ZALSryZh2iY_MbbC)\n";
echo "   Numéro: $phoneNumber\n";
echo "   Montant: 100 XOF (RÉELS - seront débités)\n\n";

echo "Voulez-vous continuer? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'y') {
    echo "❌ Test annulé\n";
    exit(0);
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "🚀 Création de la transaction LIVE...\n";
echo str_repeat("=", 70) . "\n\n";

$transactionData = [
    'description' => 'Test Vote Miss ESGIS - PAIEMENT LIVE RÉEL',
    'amount' => 100,
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

if ($httpCode !== 200 && $httpCode !== 201) {
    echo "❌ ERREUR: HTTP $httpCode\n";
    echo "Réponse: $response\n";
    exit(1);
}

$result = json_decode($response, true);

echo "✅ TRANSACTION LIVE CRÉÉE!\n\n";
echo "┌────────────────────────────────────────────────────────────────┐\n";
echo "│ DÉTAILS DE LA TRANSACTION LIVE                                │\n";
echo "├────────────────────────────────────────────────────────────────┤\n";
printf("│ ID Local       : %-42s │\n", $result['data']['id'] ?? 'N/A');
printf("│ FedaPay ID     : %-42s │\n", $result['data']['fedapay_id'] ?? 'N/A');
printf("│ Statut         : %-42s │\n", $result['data']['status'] ?? 'N/A');
printf("│ Montant        : %-42s │\n", ($result['data']['amount'] ?? 'N/A') . ' XOF');
printf("│ Téléphone      : %-42s │\n", $result['data']['customer']['phone'] ?? 'N/A');
echo "└────────────────────────────────────────────────────────────────┘\n\n";

$fedapayId = $result['data']['fedapay_id'] ?? null;
$paymentUrl = $result['data']['payment_url'] ?? null;

echo "🌐 URL DE PAIEMENT LIVE:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "$paymentUrl\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "⚠️  IMPORTANT - PAIEMENT RÉEL ⚠️\n";
echo str_repeat("=", 70) . "\n";
echo "1. Cette URL mène vers un VRAI paiement FedaPay LIVE\n";
echo "2. Les 100 XOF seront VRAIMENT débités de votre compte\n";
echo "3. Ouvrez l'URL dans votre navigateur\n";
echo "4. Choisissez MTN ou Moov Money\n";
echo "5. Entrez le numéro: 01 61 80 49 72\n";
echo "6. Validez avec votre PIN\n";
echo "7. Les 100 XOF seront débités\n\n";

echo "📊 Surveiller le paiement:\n";
echo "   tail -f /home/admin/monea-pay/api/logs/webhook.log\n";
echo "   tail -f /home/admin/monea-pay/api/logs/payments.log\n\n";

echo "🔍 Vérifier le statut:\n";
echo "   curl -H 'X-API-Key: $apiKey' \\\n";
echo "     $apiUrl/transactions/$fedapayId | jq '.data.status'\n\n";

echo str_repeat("=", 70) . "\n";
echo "✅ Transaction LIVE prête: ID $fedapayId\n";
echo "💰 Montant à payer: 100 XOF RÉELS\n";
echo "📱 Numéro: +229 01 61 80 49 72\n";
echo str_repeat("=", 70) . "\n";

// Sauvegarder l'ID pour vérification ultérieure
file_put_contents(__DIR__ . '/last_live_transaction.txt', json_encode([
    'id' => $fedapayId,
    'payment_url' => $paymentUrl,
    'amount' => 100,
    'phone' => $phoneNumber,
    'created_at' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT));

echo "\n💾 ID de transaction sauvegardé dans: last_live_transaction.txt\n";
