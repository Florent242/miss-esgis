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
        Schema::table('vote_redirections', function (Blueprint $table) {
            $table->unsignedBigInteger('source_miss_id')->nullable()->after('target_miss_id');
            $table->foreign('source_miss_id')->references('id')->on('misses')->onDelete('cascade');
            
            // Ajouter un index pour les performances
            $table->index(['source_miss_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_redirections', function (Blueprint $table) {
            $table->dropForeign(['source_miss_id']);
            $table->dropIndex(['source_miss_id', 'active']);
            $table->dropColumn('source_miss_id');
        });
    }
};