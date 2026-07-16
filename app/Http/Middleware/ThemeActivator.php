<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeActivator
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->isInstalled()) {
            return $next($request);
        }

        // Do not load theme if API request or App is running in console
        if ($request->expectsJson() || app()->runningInConsole()) {
            return $next($request);
        }

        app()->getCurrentTheme()?->activate();

        return $next($request);
    }
}
