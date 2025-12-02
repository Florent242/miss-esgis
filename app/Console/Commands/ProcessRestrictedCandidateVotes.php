<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Vote;
use App\Models\Miss;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessRestrictedCandidateVotes extends Command
{
    protected $signature = 'votes:process-restricted';
    protected $description = 'Créer les votes pour les transactions completed dont la candidate est devenue active';

    public function handle()
    {
        $this->info('🔍 Recherche des transactions à traiter...');

        // Chercher les transactions completed sans votes créés pour des candidates maintenant actives
        $transactions = Transaction::where('statut', 'completed')
            ->where('methode', 'fedapay')
            ->whereHas('miss', function($query) {
                $query->where('statut', 'active');
            })
            ->whereDoesntHave('votes')
            ->get();

        if ($transactions->isEmpty()) {
            $this->info('✅ Aucune transaction à traiter');
            return 0;
        }

        $this->info("📋 {$transactions->count()} transaction(s) trouvée(s)");

        $processed = 0;

        foreach ($transactions as $transaction) {
            $this->line("\n🔄 Transaction: {$transaction->reference}");
            $this->line("   Candidate: Miss #{$transaction->miss_id}");
            $this->line("   Montant: {$transaction->montant} XOF");

            try {
                DB::beginTransaction();

                // Calculer le nombre de votes (100 FCFA = 1 vote)
                $voteCount = intval($transaction->montant / 100);
                $this->line("   Votes à créer: {$voteCount}");

                // Créer les votes
                for ($i = 0; $i < $voteCount; $i++) {
                    Vote::create([
                        'miss_id' => $transaction->miss_id,
                        'transaction_id' => $transaction->id,
                        'moyen_paiement' => 'fedapay',
                        'montant' => 100,
                    ]);
                }

                DB::commit();

                $this->info("   ✅ {$voteCount} vote(s) créé(s) !");
                $processed++;

                Log::info('Restricted candidate votes processed', [
                    'reference' => $transaction->reference,
                    'votes_created' => $voteCount,
                    'miss_id' => $transaction->miss_id
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("   ❌ Erreur: " . $e->getMessage());
            }
        }

        $this->info("\n📊 Résumé:");
        $this->info("   ✅ Traités: {$processed}");

        return 0;
    }
}
