<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\VoteLog;
use App\Models\Vote;
use App\Models\Miss;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

class DiagnoseVoteSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:diagnose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose the vote management system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 DIAGNOSTIC DU SYSTÈME DE GESTION DES VOTES');
        $this->newLine();

        // 1. Base de données
        $this->line('📊 BASE DE DONNÉES');
        $this->checkDatabase();
        $this->newLine();

        // 2. Comptes SuperMod
        $this->line('👥 COMPTES SUPERMOD');
        $this->checkSuperMods();
        $this->newLine();

        // 3. Routes
        $this->line('🛣️  ROUTES');
        $this->checkRoutes();
        $this->newLine();

        // 4. Logs
        $this->line('📝 LOGS DE REDIRECTION');
        $this->checkLogs();
        $this->newLine();

        // 5. Statistiques
        $this->line('📈 STATISTIQUES');
        $this->showStats();
        $this->newLine();

        $this->info('✅ Diagnostic terminé');
    }

    private function checkDatabase()
    {
        $checks = [
            'Table vote_logs' => Schema::hasTable('vote_logs'),
            'Colonne admins.role' => Schema::hasColumn('admins', 'role'),
            'Model VoteLog' => class_exists('App\Models\VoteLog'),
            'Model Admin étendu' => method_exists(Admin::class, 'isSuperMod'),
        ];

        foreach ($checks as $name => $status) {
            $this->line('  ' . ($status ? '✅' : '❌') . ' ' . $name);
        }
    }

    private function checkSuperMods()
    {
        $supermods = Admin::where('role', 'supermod')->get();
        
        if ($supermods->isEmpty()) {
            $this->warn('  ⚠️  Aucun SuperMod trouvé');
        } else {
            foreach ($supermods as $sm) {
                $this->line("  ✅ {$sm->nom} ({$sm->email})");
            }
        }
    }

    private function checkRoutes()
    {
        $routes = [
            'vm.index',
            'vm.redirect',
            'vm.auto.enable',
            'vm.auto.disable',
            'vm.votes'
        ];

        foreach ($routes as $route) {
            $exists = Route::has($route);
            $this->line('  ' . ($exists ? '✅' : '❌') . ' ' . $route);
        }
    }

    private function checkLogs()
    {
        $totalLogs = VoteLog::count();
        $recentLogs = VoteLog::where('redirected_at', '>=', now()->subDays(7))->count();
        
        $this->line("  📊 Total des logs: {$totalLogs}");
        $this->line("  📊 Logs derniers 7 jours: {$recentLogs}");
        
        if ($totalLogs > 0) {
            $lastLog = VoteLog::orderBy('redirected_at', 'desc')->first();
            $this->line("  🕐 Dernière redirection: {$lastLog->redirected_at}");
        }
    }

    private function showStats()
    {
        $totalVotes = Vote::count();
        $totalCandidates = Miss::where('statut', 'active')->count();
        $totalRedirected = VoteLog::count();
        
        $this->line("  🗳️  Total votes: {$totalVotes}");
        $this->line("  👸 Candidates actives: {$totalCandidates}");
        $this->line("  🔄 Votes redirigés: {$totalRedirected}");
        
        if ($totalVotes > 0 && $totalRedirected > 0) {
            $percentage = round(($totalRedirected / $totalVotes) * 100, 2);
            $this->line("  📊 Taux de redirection: {$percentage}%");
        }
    }
}
