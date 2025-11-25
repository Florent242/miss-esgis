<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentSandbox;

class ExpireOldPayments extends Command
{
    protected $signature = 'payments:expire';
    protected $description = 'Expire old pending payments';

    public function handle()
    {
        $expired = PaymentSandbox::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Aucun paiement à expirer');
            return;
        }

        foreach ($expired as $payment) {
            $payment->status = 'expired';
            $payment->save();
        }

        $this->info("✅ {$expired->count()} paiement(s) expiré(s)");
    }
}
