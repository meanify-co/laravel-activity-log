<?php

namespace Meanify\LaravelActivityLog\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Meanify\LaravelActivityLog\Observers\CrudObserver;

class MeanifyLaravelActivityLogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //Config
        $this->publishes([
            __DIR__.'/../Config/meanify-laravel-activity-log.php' => config_path('meanify-laravel-activity-log.php'),
        ], 'meanify-configs');

        //migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__ . '/../Database/migrations' => database_path('migrations'),
        ], 'meanify-migrations');

        //Models
        $this->publishes([
            __DIR__ . '/../../src/Models/ActivityLog.php'    => app_path('Models/ActivityLog.php'),
            __DIR__ . '/../../src/Models/RequestLog.php'     => app_path('Models/RequestLog.php'),
        ], 'meanify-models');

        //headers
        Request::macro('meanify', function () {
            return new class {
                public function headers(): \Illuminate\Support\Collection
                {
                    return collect(request()->headers->all())
                        ->filter(fn ($_, $key) => str_starts_with($key, 'x-mfy-'))
                        ->map(fn ($value) => $value[0] ?? null);
                }
                public function setHeader(string $key, mixed $value): void
                {
                    request()->headers->set("x-mfy-{$key}", $value);
                }
            };
        });
    }

    public function register(): void
    {
        $this->commands([
            \Meanify\LaravelActivityLog\Commands\ActivityLogListCommand::class,
            \Meanify\LaravelActivityLog\Commands\ActivityLogPruneCommand::class,
            \Meanify\LaravelActivityLog\Commands\ActivityLogTestCommand::class,
            \Meanify\LaravelActivityLog\Commands\ActivityLogStatsCommand::class,
        ]);

        $this->mergeConfigFrom(__DIR__.'/../Config/meanify-laravel-activity-log.php', 'meanify-laravel-activity-log');
    }
}