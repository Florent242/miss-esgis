<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VoteLog;
use Illuminate\Support\Facades\DB;

class CleanVoteLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'votes:clean-logs {--all : Clean all logs} {--days=30 : Clean logs older than X days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean vote redirection logs for maintenance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            if ($this->confirm('This will delete ALL vote logs. Are you sure?')) {
                $count = VoteLog::count();
                DB::table('vote_logs')->truncate();
                $this->info("✅ All vote logs cleaned ({$count} records deleted)");
            } else {
                $this->warn('Operation cancelled');
            }
        } else {
            $days = $this->option('days');
            $date = now()->subDays($days);
            $count = VoteLog::where('redirected_at', '<', $date)->count();
            
            if ($count > 0) {
                if ($this->confirm("This will delete {$count} logs older than {$days} days. Continue?")) {
                    VoteLog::where('redirected_at', '<', $date)->delete();
                    $this->info("✅ Cleaned {$count} old vote logs");
                } else {
                    $this->warn('Operation cancelled');
                }
            } else {
                $this->info("No logs found older than {$days} days");
            }
        }
    }
}
