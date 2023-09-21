<?php

namespace App\Http\Middleware;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
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

        $conference = Conference::query()
            ->where('path', $request->route()->parameter('conference'))
            ->first();

        if (! $conference) {
            return abort(404);
        }

        switch ($conference->status) {
            case ConferenceStatus::Current:
                return redirect('current');
                break;
            case ConferenceStatus::Upcoming:
                return abort(404);
                break;
        }

        app()->setCurrentConference($conference);
        app()->scopeCurrentConference();

        View::share('currentConference', $conference);

        return $next($request);
    }
}
