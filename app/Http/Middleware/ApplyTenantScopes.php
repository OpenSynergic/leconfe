<?php

namespace App\Http\Middleware;

use App\Models\Speaker;
use App\Models\Submission;
use App\Models\Topic;
use App\Models\Venue;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
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
        $conference = Filament::getTenant();

        // All model scopes for conference are applied here.
        Submission::addGlobalScope(
            fn (Builder $query) => $query->whereBelongsTo($conference),
        );

        Topic::addGlobalScope(
            fn (Builder $query) => $query->whereBelongsTo($conference),
        );

        Venue::addGlobalScope(
            fn (Builder $query) => $query->whereBelongsTo($conference),
        );

        Speaker::addGlobalScope(
            fn (Builder $query) => $query->whereBelongsTo($conference),
        );

        return $next($request);
    }
}
