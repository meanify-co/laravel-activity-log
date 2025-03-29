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
        $this->publishes([
            __DIR__.'/../Config/meanify-laravel-activity-log.php' => config_path('meanify-laravel-activity-log.php'),
        ], 'meanify-configs');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Model::observe(CrudObserver::class);

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
        $this->mergeConfigFrom(__DIR__.'/../Config/meanify-laravel-activity-log.php', 'meanify-laravel-activity-log');
    }
}