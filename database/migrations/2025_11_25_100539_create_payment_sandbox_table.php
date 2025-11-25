<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_sandbox', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('miss_id')->constrained('misses')->onDelete('cascade');
            $table->enum('operator', ['mtn', 'moov', 'celtiis']);
            $table->string('phone_number', 20);
            $table->decimal('amount', 10, 2);
            $table->integer('vote_count');
            $table->enum('status', ['pending', 'confirmed', 'failed', 'expired'])->default('pending');
            $table->string('momo_number', 20)->comment('Numéro MoMo de réception');
            $table->text('sms_content')->nullable();
            $table->timestamp('sms_received_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_sandbox');
    }
};
