<?php

namespace Meanify\LaravelActivityLog\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    /**
     * Table's name of the model
     *
     * @var string
     */
    protected $table = 'requests_logs';

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
        'telescope_uuid',
        'ip_address',
        'method',
        'uri',
        'status_code',
        'payload',
        'response',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var string[]
     */
    protected $casts = [
        'payload' => 'object',
        'response' => 'object',
    ];
}
