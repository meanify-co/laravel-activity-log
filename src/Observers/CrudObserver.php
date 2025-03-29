<?php

namespace Meanify\LaravelActivityLog\Observers;

use Illuminate\Database\Eloquent\Model;
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
        $this->log('created', $model, null, null, $model->toArray());
    }

    /**
     * @param Model $model
     * @return void
     */
    public function updated(Model $model): void
    {
        $original = $model->getOriginal();
        $changes  = $model->getChanges();
        unset($changes['updated_at'], $changes['created_at']);

        if (!empty($changes)) {
            $changeSet = collect($changes)->mapWithKeys(fn ($new, $key) => [
                $key => [
                    'old' => $original[$key] ?? null,
                    'new' => $new,
                ]
            ])->toArray();

            $this->log('updated', $model, $original, $changeSet, $model->toArray());
        }
    }

    /**
     * @param Model $model
     * @return void
     */
    public function deleted(Model $model): void
    {
        $this->log('deleted', $model, $model->getOriginal(), null, null);
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
        if (config('meanify-laravel-activity-log.crud_enabled', true))
        {
            $class     = get_class($model);
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
                'user_id' => request()->meanify()->headers()->get('user-id'),
                'account_id' => request()->meanify()->headers()->get('account-id'),
                'request_uuid' => request()->meanify()->headers()->get('request-uuid'),
                'ip_address' => request()->meanify()->headers()->get('request-ip'),
            ]);
        }

    }
}