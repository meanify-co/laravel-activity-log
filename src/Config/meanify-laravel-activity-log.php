<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable CRUD Activity Logging
    |--------------------------------------------------------------------------
    |
    | Define whether changes to Eloquent models (create, update, delete)
    | should be logged. This is commonly used for tracking model history.
    |
    */
    'crud_enabled'    => env('MEANIFY_LARAVEL_ACTIVITY_LOG_CRUD_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Enable Request Logging (Only HTTP)
    |--------------------------------------------------------------------------
    |
    | Define whether all HTTP requests should be logged. Requests from CLI,
    | jobs, or system-level operations will be ignored by default.
    |
    */
    'request_enabled' => env('MEANIFY_LARAVEL_ACTIVITY_LOG_REQUEST_ENABLED', true),



    /*
    |--------------------------------------------------------------------------
    | Hidden Payload Fields
    |--------------------------------------------------------------------------
    | These fields will be removed from the request payload before logging
    | for privacy and security reasons. Applies to all requests.
    */
    'hidden_payload_fields' => [
        '_token',
        'password',
        'new_password',
        'current_password',
        'confirm_password',
        'new_password_confirmation',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models to ignore
    |--------------------------------------------------------------------------
    |
    | Set the class path, short name, or fully qualified name of models
    | to ignore when logging CRUD operations.
    |
    */
    'ignored_models' => [
        // \Laravel\Telescope\Models\EntryModel::class,
        // \Laravel\Pulse\PulseRecord::class,
        // \App\Models\Job::class,
        // \App\Models\Session::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | Ignore changes based on model and column
    |--------------------------------------------------------------------------
    |
    | Define specific columns or logic per model to ignore update events.
    | If all changes in a request match the ignore list or the condition
    | returns true, the update will be skipped.
    |
    */
    'ignore_changes' => [
        App\Models\User::class => [
            'last_login_at',
            'password',
        ],

        //Logic example
        App\Models\Payment::class => fn ($changes) =>
            count($changes) === 1 && isset($changes['status']) && $changes['status'] === 'processing',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore changes made by the system (CLI, Jobs, etc)
    |--------------------------------------------------------------------------
    |
    | When enabled, prevents logging if the request was made via CLI or
    | background jobs. You may optionally define exceptions by model.
    |
    */
    'ignore_system_changes' => [
        'enabled' => true,

        // Optional: exceptions that should be logged even if via system
        'except' => [
            App\Models\ImportantAudit::class => true,

            App\Models\Notification::class => fn ($changes) => isset($changes['status']) && $changes['status'] !== 'queued',
        ],
    ],
];