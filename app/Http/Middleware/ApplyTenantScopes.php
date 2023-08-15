<?php

namespace App\Http\Middleware;

use App\Models\Navigation;
use App\Models\Scopes\TenantScope;
use App\Models\Speaker;
use App\Models\Submission;
use App\Models\Topic;
use App\Models\Venue;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyTenantScopes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // All model scopes for conference are applied here.
        foreach ([
            Submission::class,
            Topic::class,
            Venue::class,
            Speaker::class,
            Navigation::class,
        ] as $model) {
            $model::addGlobalScope(new TenantScope);
        }

        return $next($request);
    }
}
