<?php

namespace Meanify\LaravelActivityLog\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /**
     * Table's name of the model
     *
     * @var string
     */
    protected $table = 'activity_logs';

    /**
     * The attributes that should be not changed
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are table's timestamps
     *
     * @var string[]
     */
    public $timestamps = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'account_id',
        'user_id',
        'request_uuid',
        'ip_address',
        'model',
        'model_id',
        'action',
        'original',
        'changes',
        'final',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var string[]
     */
    protected $casts = [
        'original' => 'object',
        'changes' => 'object',
        'final' => 'object',
    ];
}
