<?php

namespace App\Http\Middleware;

use App\Models\Block;
use App\Models\Navigation;
use App\Models\Participants\Participant;
use App\Models\Participants\ParticipantPosition;
use App\Models\Participants\Speaker;
use App\Models\Participants\SpeakerPosition;
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

        foreach ([
            Submission::class,
            Topic::class,
            Venue::class,
            Speaker::class,
            Navigation::class,
            Block::class,
            Speaker::class,
            SpeakerPosition::class,
        ] as $model) {
            $model::addGlobalScope(new ConferenceScope);
        }

        return $next($request);
    }
}