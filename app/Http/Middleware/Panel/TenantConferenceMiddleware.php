<?php

namespace App\Http\Middleware\Panel;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantConferenceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // app()->setCurrentConferenceId(Filament::getTenant()->getKey());
        // app()->scopeCurrentConference();

        return $next($request);
    }
}
