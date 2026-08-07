<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Analytics\AnalyticEventGenerator;

class LogAnalyticEventMiddleware
{
    public const ASSOC_TYPE_SCHEDULED_CONFERENCE = 1;
    public const ASSOC_TYPE_PROCEEDING = 2;
    public const ASSOC_TYPE_SUBMISSION = 3;
    public const ASSOC_TYPE_GALLEY = 4;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser (0ms Latency Impact).
     */
    public function terminate(Request $request, Response $response): void
    {
        // Only log successful responses (200 OK or 304 Not Modified)
        if (!in_array($response->getStatusCode(), [200, 304])) {
            return;
        }

        $assocType = null;
        $assocId = null;
        $fileType = null;

        // Extract target entity from route or context
        if ($galley = $request->route('galley')) {
            $assocType = self::ASSOC_TYPE_GALLEY;
            $assocId = is_object($galley) ? $galley->id : (int)$galley;
            $fileType = 1;
        } elseif ($proceeding = $request->route('proceeding')) {
            $assocType = self::ASSOC_TYPE_PROCEEDING;
            $assocId = is_object($proceeding) ? $proceeding->id : (int)$proceeding;
        } elseif ($submission = $request->route('submission')) {
            $assocType = self::ASSOC_TYPE_SUBMISSION;
            $assocId = is_object($submission) ? $submission->id : (int)$submission;
        } elseif (app()->bound('currentScheduledConferenceId') && app()->getCurrentScheduledConferenceId()) {
            $assocType = self::ASSOC_TYPE_SCHEDULED_CONFERENCE;
            $assocId = app()->getCurrentScheduledConferenceId();
        }

        if ($assocType && $assocId) {
            $generator = app(AnalyticEventGenerator::class);
            $generator->logEvent($request, $assocType, $assocId, $fileType);
        }
    }
}
