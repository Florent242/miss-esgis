<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN methode ENUM('kkiapay', 'momo_mtn', 'momo_moov', 'momo_celtiis') NOT NULL DEFAULT 'kkiapay'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN methode ENUM('kkiapay') NOT NULL DEFAULT 'kkiapay'");
    }
};
