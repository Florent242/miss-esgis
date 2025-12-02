<?php
/**
 * Script pour vérifier et mettre à jour les paiements FedaPay
 * Vérifie le statut approved et met à jour la BDD Laravel
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     🔍 Vérification Statut FedaPay + Mise à jour BDD          ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// API Configuration
$apiUrl = 'https://pay.aiko.qzz.io/tower-send-dev/api';
$apiKey = 'fedapay_api_key_123456789';

// Récupérer les transactions pending des dernières 48h
echo "📋 Récupération des transactions en attente...\n";
$pendingTransactions = Transaction::where('statut', 'pending')
    ->where('methode', 'fedapay')
    ->where('created_at', '>', now()->subHours(48))
    ->whereNotNull('miss_id')
    ->whereNotNull('transaction_id')
    ->orderBy('created_at', 'desc')
    ->get();

echo "   Trouvé: {$pendingTransactions->count()} transaction(s)\n\n";

if ($pendingTransactions->isEmpty()) {
    echo "✅ Aucune transaction en attente à vérifier\n";
    exit(0);
}

$processed = 0;
$failed = 0;
$alreadyProcessed = 0;

foreach ($pendingTransactions as $transaction) {
    echo str_repeat("-", 60) . "\n";
    echo "🔄 Transaction: {$transaction->reference}\n";
    echo "   FedaPay ID: {$transaction->transaction_id}\n";
    echo "   Montant: {$transaction->montant} XOF\n";
    echo "   Candidate: Miss #{$transaction->miss_id}\n";
    echo "   Créée: {$transaction->created_at}\n\n";

    // Vérifier via SDK FedaPay
    try {
        \FedaPay\FedaPay::setApiKey('sk_live_R90vA_Z7ZALSryZh2iY_MbbC');
        \FedaPay\FedaPay::setEnvironment('live');
        
        $fedaTransaction = \FedaPay\Transaction::retrieve($transaction->transaction_id);
        $status = $fedaTransaction->status;
        
        echo "   ✅ Statut récupéré via SDK FedaPay\n";
        
    } catch (\Exception $sdkError) {
        echo "   ⚠️  SDK échoué, essai cURL...\n";
        
        // Fallback: CURL vers l'API externe
        $ch = curl_init("{$apiUrl}/transactions/{$transaction->transaction_id}");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'X-API-Key: ' . $apiKey,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            echo "   ❌ Erreur API (HTTP {$httpCode})\n";
            if ($response) {
                $data = json_decode($response, true);
                echo "   Message: " . ($data['error'] ?? 'Inconnu') . "\n";
            }
            $failed++;
            continue;
        }

        $data = json_decode($response, true);
        
        if (!isset($data['data']['status'])) {
            echo "   ❌ Réponse API invalide\n";
            $failed++;
            continue;
        }

        $status = $data['data']['status'];
    }
    echo "   📊 Statut API: {$status}\n";

    // Si le statut est APPROVED
    if ($status === 'approved') {
        echo "   ✅ PAIEMENT APPROUVÉ !\n";
        
        // Vérifier si déjà traité
        if ($transaction->statut === 'completed') {
            echo "   ⚠️  Déjà traité précédemment\n";
            $alreadyProcessed++;
            continue;
        }

        try {
            DB::beginTransaction();

            // Mettre à jour la transaction
            $transaction->statut = 'completed';
            $transaction->save();
            echo "   ✓ Transaction mise à jour: completed\n";

            // Calculer le nombre de votes (98 FCFA = 1 vote)
            $voteCount = intval($transaction->montant / 98);
            echo "   ✓ Votes à créer: {$voteCount}\n";

            // Créer les votes
            for ($i = 0; $i < $voteCount; $i++) {
                Vote::create([
                    'miss_id' => $transaction->miss_id,
                    'transaction_id' => $transaction->id,
                    'moyen_paiement' => 'fedapay',
                    'montant' => 98,
                ]);
            }

            DB::commit();

            echo "   🎉 {$voteCount} VOTE(S) CRÉÉ(S) AVEC SUCCÈS !\n";
            $processed++;

        } catch (\Exception $e) {
            DB::rollBack();
            echo "   ❌ Erreur lors de la création des votes: " . $e->getMessage() . "\n";
            $failed++;
        }

    } elseif ($status === 'declined') {
        $transaction->statut = 'failed';
        $transaction->save();
        echo "   ⚠️  Paiement refusé - Statut mis à jour: failed\n";
        $failed++;

    } elseif ($status === 'canceled') {
        $transaction->statut = 'canceled';
        $transaction->save();
        echo "   ⚠️  Paiement annulé - Statut mis à jour: canceled\n";
        $failed++;

    } else {
        echo "   ⏳ Toujours en attente (status: {$status})\n";
    }

    echo "\n";
}

// Résumé
echo str_repeat("=", 60) . "\n";
echo "📊 RÉSUMÉ:\n";
echo "   ✅ Traités avec succès: {$processed}\n";
echo "   ⚠️  Déjà traités: {$alreadyProcessed}\n";
echo "   ❌ Échecs: {$failed}\n";
echo "   📋 Total vérifié: {$pendingTransactions->count()}\n";
echo str_repeat("=", 60) . "\n";

if ($processed > 0) {
    echo "\n🎉 {$processed} PAIEMENT(S) TRAITÉ(S) AVEC SUCCÈS !\n";
}

exit($failed > 0 ? 1 : 0);
