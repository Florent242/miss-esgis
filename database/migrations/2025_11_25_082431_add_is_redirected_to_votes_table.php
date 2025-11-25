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
        Schema::table('votes', function (Blueprint $table) {
            $table->boolean('is_redirected')->default(false)->after('montant');
            $table->foreignId('intended_miss_id')->nullable()->after('is_redirected')->constrained('misses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign(['intended_miss_id']);
            $table->dropColumn(['is_redirected', 'intended_miss_id']);
        });
    }
};
