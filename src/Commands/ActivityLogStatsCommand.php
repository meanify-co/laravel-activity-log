<?php

namespace Meanify\LaravelActivityLog\Commands;

use Illuminate\Console\Command;
use Meanify\LaravelActivityLog\Models\ActivityLog;
use Meanify\LaravelActivityLog\Models\RequestLog;

class ActivityLogStatsCommand extends Command
{
    protected $signature = 'meanify:activity-log:stats';

    protected $description = 'Show statistics about activity and request logs';

    public function handle(): int
    {
        $this->info('--- Activity Log Stats ---');

        $this->line('By Action:');
        $byAction = ActivityLog::selectRaw('action, COUNT(*) as total')
            ->groupBy('action')
            ->pluck('total', 'action')
            ->toArray();

        foreach ($byAction as $action => $count) {
            $this->line(" - {$action}: {$count}");
        }

        $this->line('\nTop 5 Models:');
        $byModel = ActivityLog::selectRaw('model_type, COUNT(*) as total')
            ->groupBy('model_type')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'model_type')
            ->toArray();

        foreach ($byModel as $model => $count) {
            $this->line(" - " . class_basename($model) . ": {$count}");
        }

        $totalRequests = RequestLog::count();
        $this->line("\nTotal HTTP Requests Logged: {$totalRequests}");

        return self::SUCCESS;
    }
}
