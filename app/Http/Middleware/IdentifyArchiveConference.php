<?php

namespace App\Http\Middleware;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyArchiveConference
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

        $conference = app()->getCurrentConference();

        if (! $conference) {
            return abort(404);
        }

        switch ($conference->status) {
            case ConferenceStatus::Active:
                return redirect('current');
                break;
            case ConferenceStatus::Upcoming:
                return abort(404);
                break;
        }

        return $next($request);
    }
}
