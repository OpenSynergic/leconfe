<?php

namespace App\Http\Middleware;

use App\Models\Scopes\TenantScope;
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
        Submission::addGlobalScope(new TenantScope);

        Topic::addGlobalScope(new TenantScope);

        Venue::addGlobalScope(new TenantScope);

        Speaker::addGlobalScope(new TenantScope);

        return $next($request);
    }
}
