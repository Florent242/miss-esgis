<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentSandbox;

class MonitorPendingPayments extends Command
{
    protected $signature = 'payments:monitor';
    protected $description = 'Monitor pending sandbox payments';

    public function handle()
    {
        $pending = PaymentSandbox::where('status', 'pending')
            ->where('expires_at', '>', now())
            ->get();

        $this->info('📊 PAIEMENTS EN ATTENTE');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($pending->isEmpty()) {
            $this->warn('Aucun paiement en attente');
            return;
        }

        $this->table(
            ['Référence', 'Opérateur', 'Montant', 'Votes', 'Téléphone', 'Expire dans'],
            $pending->map(function ($payment) {
                $expiresIn = now()->diffInMinutes($payment->expires_at);
                return [
                    substr($payment->reference, 0, 20) . '...',
                    strtoupper($payment->operator),
                    $payment->amount . ' FCFA',
                    $payment->vote_count,
                    $payment->phone_number,
                    $expiresIn . ' min'
                ];
            })
        );

        $this->info("\nTotal : {$pending->count()} paiement(s) en attente");
    }
}
