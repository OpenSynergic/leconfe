<?php

namespace App\Http\Middleware;

use App\Actions\Leconfe\SendTelemetry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SendTelemetryAfterRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! app()->isInstalled() || ! auth()->check()) {
            return;
        }

        try {
            SendTelemetry::dispatchOncePerDay();
        } catch (\Throwable) {
            // Telemetry must never change the result of an authenticated request.
        }
    }
}
