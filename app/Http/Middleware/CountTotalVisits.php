<?php

namespace App\Http\Middleware;

use App\Models\Conference;
use App\Website\Pages\Installation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
        if ($this->isInstallationRoute()) {
            return $next($request);
        }

        $currentConference = Conference::active();

        $this->recordVisit($request, $currentConference);

        return $next($request);
    }

    /**
     * Check if the current route is the installation route.
     *
     * @return bool
     */
    private function isInstallationRoute(): bool
    {
        return Route::getCurrentRoute()->uri === Installation::getSlug();
    }

    /**
     * Record the visit for the current conference.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conference  $conference
     * @return void
     */
    private function recordVisit(Request $request, Conference $conference): void
    {
        $conference->visit()
            ->withIp()
            ->withSession()
            ->withUser()
            ->withData([
                'user_agent' => $request->userAgent(),
            ]);
    }
}
