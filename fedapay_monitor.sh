#!/bin/bash

echo "╔═══════════════════════════════════════════════════════════════╗"
echo "║          🎯 FEDAPAY MONITORING & AUTO-UPDATE                  ║"
echo "╚═══════════════════════════════════════════════════════════════╝"
echo ""

# 1. Vérifier et traiter les paiements en attente
echo "🔄 Vérification des paiements en attente..."
php /var/www/miss-esgis/check_and_update_fedapay.php

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo ""

# 2. Afficher les statistiques
echo "📊 STATISTIQUES DES TRANSACTIONS FEDAPAY"
echo "─────────────────────────────────────────────────────────────"

php << 'EOFPHP'
<?php
require '/var/www/miss-esgis/vendor/autoload.php';
$app = require_once '/var/www/miss-esgis/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$stats = DB::table('transactions')
    ->select('statut', DB::raw('COUNT(*) as count'), DB::raw('SUM(montant) as total'))
    ->where('methode', 'fedapay')
    ->groupBy('statut')
    ->get();

foreach ($stats as $stat) {
    $icon = match($stat->statut) {
        'completed' => '✅',
        'pending' => '⏳',
        'failed' => '❌',
        'canceled' => '🚫',
        default => '❓'
    };
    printf("%s %-12s: %3d transactions | %7d FCFA\n", 
        $icon, 
        strtoupper($stat->statut), 
        $stat->count, 
        $stat->total ?? 0
    );
}

echo "\n";

$votes = DB::table('votes')
    ->where('moyen_paiement', 'fedapay')
    ->count();

echo "🗳️  VOTES FEDAPAY CRÉÉS: {$votes}\n";

EOFPHP

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo ""

# 3. Logs récents
echo "📝 LOGS RÉCENTS (FedaPay)"
echo "─────────────────────────────────────────────────────────────"
tail -15 /var/www/miss-esgis/storage/logs/laravel.log | grep -E "FedaPay|fedapay" || echo "Aucun log récent"

echo ""
echo "✅ Monitoring terminé!"
