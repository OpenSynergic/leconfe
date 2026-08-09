<?php

namespace App\Http\Middleware;

use App\Application;
use App\Facades\Plugin;
use App\Models\Conference;
use App\Models\ScheduledConference;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectConferenceContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $this->detect($request);

        if (app()->isInstalled()) {
            Plugin::ensureCurrentContextInitialized();
        }

        return $next($request);
    }

    public function detect(Request $request): void
    {
        app()->resetCurrentContext();

        if (! app()->isInstalled()) {
            return;
        }

        app()->scopeCurrentConference();
        app()->scopeCurrentScheduledConference();

        $segments = $request->segments();
        $conferencePath = $segments[0] ?? null;
        $isScheduledPath = ($segments[1] ?? null) === 'scheduled' && filled($segments[2] ?? null);

        if ($conferencePath) {
            $conference = Conference::query()
                ->with(['media', 'meta'])
                ->where('path', $conferencePath)
                ->first();

            app()->setCurrentConferenceId($conference?->getKey() ?? Application::CONTEXT_WEBSITE);

            if (! $conference && $isScheduledPath) {
                abort(404);
            }

            if ($conference && $isScheduledPath) {
                $scheduledConference = ScheduledConference::findByConferenceAndExactPath($conference, $segments[2]);

                if (! $scheduledConference) {
                    abort(404);
                }

                app()->setCurrentScheduledConferenceId($scheduledConference->getKey());
            }
        }

    }
}
