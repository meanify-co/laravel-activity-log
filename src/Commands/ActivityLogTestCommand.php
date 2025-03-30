<?php

namespace Meanify\LaravelActivityLog\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Date;
use Meanify\LaravelActivityLog\Models\ActivityLog;

class ActivityLogTestCommand extends Command
{
    protected $signature = 'meanify:activity-log:test
                            {--model=User : Model class name}
                            {--id=1 : Model ID to simulate}
                            {--action=updated : Action type (created|updated|deleted|restored|force_deleted)}';

    protected $description = 'Simulate a test activity log record';

    public function handle(): int
    {
        $class = Str::start($this->option('model'), 'App\\Models\\');
        $id = $this->option('id');
        $action = $this->option('action') ?? 'updated';

        $log = ActivityLog::create([
            'model'        => $class,
            'model_id'     => $id,
            'action'       => $action,
            'original'     => ['name' => 'Old Value'],
            'changes'      => ['name' => ['old' => 'Old Value', 'new' => 'New Value']],
            'final'        => ['name' => 'New Value'],
            'user_id'      => '1',
            'account_id'   => '1',
            'request_uuid' => (string) Str::uuid(),
            'ip_address'   => '127.0.0.1',
            'created_at'   => Date::now(),
        ]);

        $this->info("Simulated activity log created with ID: {$log->id}");

        return self::SUCCESS;
    }
}