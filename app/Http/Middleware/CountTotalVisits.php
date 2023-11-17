<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Conference;
use Illuminate\Http\Request;
use App\Website\Pages\Installation;
use Illuminate\Support\Facades\Route;
use App\Models\Scopes\ConferenceScope;
use Symfony\Component\HttpFoundation\Response;

class CountTotalVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Route::getCurrentRoute()->uri === Installation::getSlug()) {
            return $next($request);
        }

        $currentConference = Conference::active();

        $currentConference->visit()
            ->withIp()
            ->withSession()
            ->withUser()
            ->withData([
                'user_agent' => $request->userAgent(),
            ]);

        return $next($request);
    }
}