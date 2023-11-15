<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Conference;
use Illuminate\Http\Request;
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
        // Get the active conference
        $conference = Conference::active();

        // Log the visit details
        $conference->visit()
            ->withIp()
            ->withSession()
            ->withUser()
            ->withData([
                'user_agent' => $request->userAgent(),
            ]);

        return $next($request);
    }
}
