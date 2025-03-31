<?php

namespace Meanify\LaravelActivityLog\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Meanify\LaravelActivityLog\Models\ActivityLog;
use Meanify\LaravelActivityLog\Models\RequestLog;

class CrudObserver
{
    /**
     * @param Model $model
     * @return void
     */
    public function created(Model $model): void
    {
        if ($this->shouldIgnore($model, 'created')) {
            return;
        }

        $this->log('created', $model, null, null, $model->toArray());
    }

    /**
     * @param Model $model
     * @return void
     */
    public function updated(Model $model): void
    {
        $original = $model->getOriginal();
        $changes = $model->getChanges();
        unset($changes['updated_at'], $changes['created_at']);

        if (empty($changes)) {
            return;
        }

        if ($this->shouldIgnore($model, 'updated', $changes)) {
            return;
        }

        $changeSet = collect($changes)->mapWithKeys(fn ($new, $key) => [
            $key => [
                'old' => $original[$key] ?? null,
                'new' => $new,
            ]
        ])->toArray();

        $this->log('updated', $model, $original, $changeSet, $model->toArray());
    }

    /**
     * @param Model $model
     * @return void
     */
    public function deleted(Model $model): void
    {
        if ($this->shouldIgnore($model, 'deleted')) {
            return;
        }

        $this->log('deleted', $model, $model->getOriginal(), null, null);
    }

    /**
     * @param Model $model
     * @return void
     */
    public function forceDeleted(Model $model): void
    {
        if ($this->shouldIgnore($model, 'force_deleted')) {
            return;
        }

        $this->log('force_deleted', $model, $model->getOriginal(), null, null);
    }

    /**
     * @param Model $model
     * @return void
     */
    public function restored(Model $model): void
    {
        if ($this->shouldIgnore($model, 'restored')) {
            return;
        }

        $this->log('restored', $model, null, null, $model->toArray());
    }

    /**
     * @param Model $model
     * @param string $action
     * @param array $changes
     * @return bool
     */
    protected function shouldIgnore(Model $model, string $action, array $changes = []): bool
    {
        $class = get_class($model);

        // Model-level flag
        if (property_exists($model, 'meanify_log_enabled') && $model::$meanify_log_enabled === false) {
            return true;
        }
        
        // Ignore specific actions
        if (property_exists($model, 'meanify_log_ignore_actions') && in_array($action, $model::$meanify_log_ignore_actions ?? [])) {
            return true;
        }

        // Ignore changes (columns or closure)
        if (!empty($changes)) {
            $ignore = $model::$meanify_log_ignore_changes ?? config("meanify-laravel-activity-log.ignore_changes.{$class}");
            if (is_array($ignore) && !array_diff(array_keys($changes), $ignore)) {
                return true;
            }
            if (is_callable($ignore) && $ignore($changes)) {
                return true;
            }
        }

        // Ignore if from system (CLI or background)
        $ignore_system = $model::$meanify_log_ignore_system ?? config('meanify-laravel-activity-log.ignore_system_changes.enabled', false);

        if ($ignore_system && App::runningInConsole()) {
            // Check exceptions
            $except = $model::$meanify_log_ignore_system_except ?? config('meanify-laravel-activity-log.ignore_system_changes.except')[$class] ?? null;

            if (is_array($except) && !array_diff(array_keys($changes), $except)) {
                return false; // explicitly allow
            }
            if (is_callable($except) && $except($changes)) {
                return false; // explicitly allow
            }

            if (method_exists($model, 'meanifyLogIgnoreSystemCondition')) {
                return !$model::meanifyLogIgnoreSystemCondition($changes);
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $action
     * @param Model $model
     * @param array|null $original
     * @param array|null $changes
     * @param array|null $final
     * @return void
     */
    protected function log(string $action, Model $model, ?array $original, ?array $changes, ?array $final): void
    {
        $class = get_class($model);
        $shortName = class_basename($class);

        if (
            in_array($class, config('meanify-laravel-activity-log.ignored_models', [])) ||
            in_array($shortName, config('meanify-laravel-activity-log.ignored_models', [])) ||
            in_array($class, [
                ActivityLog::class,
                RequestLog::class,
            ])
        ) {
            return;
        }

        ActivityLog::create([
            'model' => $class,
            'model_id' => $model->getKey(),
            'action' => $action,
            'original' => $original,
            'changes' => $changes,
            'final' => $final,
            'user_id' => request()->header('x-mfy-user-id'),
            'account_id' => request()->header('x-mfy-account-id'),
            'request_uuid' => request()->header('x-mfy-request-uuid'),
            'ip_address' => request()->header('x-mfy-request-ip'),
        ]);
    }
}
