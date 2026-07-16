<?php

namespace App\Http\Middleware;

use App\Models\Conference;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToConference
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

        $site = app()->getSite();

        if (! $site->getMeta('conference_redirect')) {
            return $next($request);
        }

        $conference = Conference::find($site->getMeta('conference_redirect'));

        if (! $conference) {
            return $next($request);
        }

        return redirect()->to($conference->getHomeUrl());
    }
}
