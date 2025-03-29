<?php

return [

    'crud_enabled'    => env('MEANIFY_LARAVEL_ACTIVITY_LOG_CRUD_ENABLED', true),

    'request_enabled' => env('MEANIFY_LARAVEL_ACTIVITY_LOG_REQUEST_ENABLED', true),

    /*
     |--------------------------------------------------------------------------
     | Models to ignore
     |--------------------------------------------------------------------------
     |
     | Set path from models to ignore activity log.
     | Suggestions: add models from Telescope, Pulse and Jobs.
     |
     */
    'ignored_models' => [
        //\Laravel\Telescope\Models\EntryModel::class,
        //\Laravel\Pulse\PulseRecord::class,
        //\App\Models\Job::class,
        //\App\Models\Session::class,
    ],
];