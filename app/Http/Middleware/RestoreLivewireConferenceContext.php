<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestoreLivewireConferenceContext
{
    public function __construct(protected DetectConferenceContext $detector) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request === request()) {
            return $next($request);
        }

        return $this->detector->handle($request, $next);
    }
}
