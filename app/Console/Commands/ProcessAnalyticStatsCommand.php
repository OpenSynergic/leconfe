<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\Analytics\ProcessAnalyticStatsAction;

class ProcessAnalyticStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leconfe:process-analytic-stats {--force : Force processing of active log file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process daily raw log files, apply COUNTER double-click filtering, and load data into analytic_metrics DB';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting AnalyticStats log ETL processing...');

        $action = app(ProcessAnalyticStatsAction::class);
        $processedCount = $action->handle((bool) $this->option('force'));

        $this->info("AnalyticStats ETL process completed successfully! Processed {$processedCount} log file(s).");

        return Command::SUCCESS;
    }
}
