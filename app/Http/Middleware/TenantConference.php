<?php

namespace App\Http\Middleware;

use App\Models\Block;
use App\Models\Navigation;
use App\Models\ParticipantPosition;
use App\Models\Scopes\ConferenceScope;
use App\Models\Submission;
use App\Models\Topic;
use App\Models\Venue;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantConference
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setCurrentConference(Filament::getTenant());
        app()->scopeCurrentConference();
        
        return $next($request);
    }
}
