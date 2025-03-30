<?php

namespace Meanify\LaravelActivityLog\Commands;

use Illuminate\Console\Command;
use Meanify\LaravelActivityLog\Models\ActivityLog;
use Meanify\LaravelActivityLog\Models\RequestLog;
use Illuminate\Support\Carbon;

class ActivityLogPruneCommand extends Command
{
    protected $signature = 'meanify:activity-log:prune {--days=30 : Delete logs older than X days} {--force : Skip confirmation}';

    protected $description = 'Delete old activity and request logs';

    /**
     * @return int
     */
    public function handle(): int
    {
        $days = (int) $this->option('days') ?: 30;
        $cutoff = Carbon::now()->subDays($days);

        if (!$this->option('force')) {
            if (!$this->confirm("Delete logs older than {$days} days (before {$cutoff->toDateTimeString()})?")) {
                $this->info('Prune cancelled.');
                return self::SUCCESS;
            }
        }

        $activityCount = ActivityLog::where('created_at', '<', $cutoff)->count();
        $requestCount = RequestLog::where('created_at', '<', $cutoff)->count();

        ActivityLog::where('created_at', '<', $cutoff)->delete();
        RequestLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$activityCount} activity logs and {$requestCount} request logs older than {$days} days.");

        return self::SUCCESS;
    }
}