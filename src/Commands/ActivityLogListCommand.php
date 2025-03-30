<?php

namespace Meanify\LaravelActivityLog\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Meanify\ActivityLog\Models\ActivityLog;

class ActivityLogListCommand extends Command
{
    protected $signature = 'meanify:activity-log:list
                            {--model= : Filter by model class (e.g., User)}
                            {--action= : created|updated|deleted|restored|force_deleted}
                            {--user= : Filter by user_id}
                            {--account= : Filter by account_id}
                            {--from= : Start date (Y-m-d)}
                            {--to= : End date (Y-m-d)}
                            {--limit= : Max records to return (default 50)}';

    protected $description = 'List activity logs with optional filters';

    /**
     * @return int
     */
    public function handle(): int
    {
        $query = ActivityLog::query();

        if ($model = $this->option('model')) {
            $model = Str::start($model, 'App\\Models\\');
            $query->where('model', $model);
        }

        if ($action = $this->option('action')) {
            $query->where('action', $action);
        }

        if ($user = $this->option('user')) {
            $query->where('user_id', $user);
        }

        if ($account = $this->option('account')) {
            $query->where('account_id', $account);
        }

        if ($from = $this->option('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $this->option('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $limit = (int) $this->option('limit') ?: 50;

        $logs = $query->latest()->limit($limit)->get();

        if ($logs->isEmpty()) {
            $this->info('No activity logs found for the given filters.');
            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Model', 'Model ID', 'Action', 'User ID', 'Account ID', 'Date'],
            $logs->map(fn ($log) => [
                $log->id,
                class_basename($log->model),
                $log->model_id,
                $log->action,
                $log->user_id,
                $log->account_id,
                $log->created_at->toDateTimeString(),
            ])
        );

        return self::SUCCESS;
    }
}
