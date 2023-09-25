<?php

namespace App\Http\Middleware;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class IdentifyUpcomingConference
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->route()->hasParameter('conference')) {
            return abort(404);
        }

        $conference = Conference::query()
            ->where('path', $request->route()->parameter('conference'))
            ->first();

        if (! $conference) {
            return abort(404);
        }

        switch ($conference->status) {
            case ConferenceStatus::Active:
                return redirect('current');
                break;
            case ConferenceStatus::Archived:
                // redirect to archive page with the same parameter
                return redirect('archive/'.$conference->path);
                break;
        }

        app()->setCurrentConference($conference);
        app()->scopeCurrentConference();

        return $next($request);
    }
}
