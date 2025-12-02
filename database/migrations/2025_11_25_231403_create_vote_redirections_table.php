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
        Schema::create('vote_redirections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_miss_id');
            $table->integer('priority')->default(1); // 1 = haute priorité, 2 = moyenne, 3 = basse
            $table->integer('weight')->default(1); // Poids de distribution (ex: 3 = reçoit 3x plus)
            $table->integer('max_votes')->nullable(); // Limite de votes à rediriger (null = illimité)
            $table->integer('votes_redirected')->default(0); // Compteur
            $table->boolean('active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->foreign('target_miss_id')->references('id')->on('misses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_redirections');
    }
};
