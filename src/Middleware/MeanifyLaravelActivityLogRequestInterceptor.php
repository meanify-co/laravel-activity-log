<?php

namespace Meanify\LaravelActivityLog\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Meanify\LaravelActivityLog\Models\RequestLog;

class MeanifyLaravelActivityLogRequestInterceptor
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->headers->has('x-mfy-request-uuid'))
        {
            $request->headers->set('x-mfy-request-uuid', (string) Str::uuid());
        }

        if (!$request->headers->has('x-mfy-request-started-at'))
        {
            $request->headers->set('x-mfy-request-started-at', now()->toIso8601String());
        }

        if (!$request->headers->has('x-mfy-request-ip'))
        {
            $request->headers->set('x-mfy-request-ip', request()->getClientIp());
        }

        $response = $next($request);

        if (config('meanify-laravel-activity-log.request_enabled', true)) {
            RequestLog::create([
                'method'         => $request->method(),
                'uri'            => $request->path(),
                'status_code'    => $response->getStatusCode(),
                'user_id'        => $request->meanify()->headers()->get('user-id'),
                'account_id'     => $request->meanify()->headers()->get('account-id'),
                'request_uuid'   => $request->meanify()->headers()->get('request-uuid'),
                'telescope_uuid' => $request->header('x-request-id'),
                'ip_address'     => $request->meanify()->headers()->get('request-ip'),
                'payload'        => $request->except(config('meanify-laravel-activity-log.hidden_payload_fields', [])),
                'response'       => method_exists($response, 'getContent') ? json_decode($response->getContent(), true) : null,
                'started_at'     => $request->attributes->get('x-mfy-request-started-at'),
                'finished_at'    => now(),
            ]);
        }

        return $response;
    }
}