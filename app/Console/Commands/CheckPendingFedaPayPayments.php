<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Vote;
use App\Services\FedaPayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPendingFedaPayPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fedapay:check-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier et traiter les paiements FedaPay en attente';

    protected $fedaPayService;

    public function __construct(FedaPayService $fedaPayService)
    {
        parent::__construct();
        $this->fedaPayService = $fedaPayService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Vérification des paiements FedaPay en attente...');

        // Récupérer les transactions pending des dernières 24h
        $pendingTransactions = Transaction::where('statut', 'pending')
            ->where('methode', 'fedapay')
            ->where('created_at', '>', now()->subHours(24))
            ->whereNotNull('miss_id')
            ->get();

        if ($pendingTransactions->isEmpty()) {
            $this->info('✅ Aucun paiement en attente');
            return 0;
        }

        $this->info("📋 {$pendingTransactions->count()} paiement(s) à vérifier");

        $processed = 0;
        $failed = 0;

        foreach ($pendingTransactions as $transaction) {
            $this->line("\n🔄 Vérification: {$transaction->reference}");

            // Chercher le fedapay_id dans les logs ou utiliser une approximation
            // Pour l'instant, on skip si pas de transaction_id
            if (!$transaction->transaction_id) {
                $this->warn("⚠️  Pas de fedapay_id pour {$transaction->reference}");
                continue;
            }

            // Vérifier le statut
            $result = $this->fedaPayService->getTransactionStatus($transaction->transaction_id);

            if (!$result['success']) {
                $this->error("❌ Erreur API pour {$transaction->reference}");
                $failed++;
                continue;
            }

            $status = $result['status'];
            $this->line("   Statut: {$status}");

            if ($status === 'approved') {
                try {
                    DB::beginTransaction();

                    // Mettre à jour la transaction
                    $transaction->statut = 'completed';
                    $transaction->save();

                    // Calculer et créer les votes (100 FCFA = 1 vote)
                    $voteCount = intval($transaction->montant / 100);
                    
                    for ($i = 0; $i < $voteCount; $i++) {
                        Vote::create([
                            'miss_id' => $transaction->miss_id,
                            'transaction_id' => $transaction->id,
                            'moyen_paiement' => 'fedapay',
                            'montant' => 100,
                        ]);
                    }

                    DB::commit();

                    $this->info("✅ {$voteCount} vote(s) créé(s) pour {$transaction->reference}");
                    $processed++;

                    Log::info('FedaPay payment processed via cron', [
                        'reference' => $transaction->reference,
                        'votes_created' => $voteCount,
                        'miss_id' => $transaction->miss_id
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("❌ Erreur: " . $e->getMessage());
                    $failed++;
                }
            } elseif ($status === 'declined' || $status === 'canceled') {
                $transaction->statut = $status === 'declined' ? 'failed' : 'canceled';
                $transaction->save();
                $this->warn("⚠️  Paiement {$status}: {$transaction->reference}");
            }
        }

        $this->info("\n📊 Résumé:");
        $this->info("   ✅ Traités: {$processed}");
        $this->info("   ❌ Échecs: {$failed}");

        return 0;
    }
}
