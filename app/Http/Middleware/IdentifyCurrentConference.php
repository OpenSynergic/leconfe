<?php

namespace App\Http\Middleware;

use App\Models\Conference;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class IdentifyCurrentConference
{
    /**
     * Handle an incoming request.
     *
     *
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $conference = Conference::current();

        if (! $conference) {
            return abort(404);
        }

        app()->setCurrentConference($conference);
        app()->scopeCurrentConference();

        View::share('currentConference', $conference);

        return $next($request);
    }
}
