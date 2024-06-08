<?php

namespace App\Http\Middleware;

use App\Application;
use App\Models\Conference;
use App\Models\Serie;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;

class DetectConference
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $app = app();
        $app->scopeCurrentConference();

        $pathInfos = explode('/', $request->getPathInfo());
        // Detect conference from URL path
        if (isset($pathInfos[1]) && !blank($pathInfos[1])) {

            $conference = Conference::where('path', $pathInfos[1])->first();

            $conference ? $app->setCurrentConferenceId($conference->getKey()) : $app->setCurrentConferenceId(Application::CONTEXT_WEBSITE);

            // Detect serie from URL path when conference is set
            if ($conference) {

                // Eager load conference relations
                $conference->load(['media', 'meta']);


                if (isset($pathInfos[3]) && !blank($pathInfos[3])) {
                    $serie = Serie::where('path', $pathInfos[3])->first();
                }

                $serie ??= $conference->currentSerie;
                if ($serie) {
                    $app->setCurrentSerieId($serie->getKey());
                    $app->scopeCurrentSerie();
                }
            }
        }

        // Scope livewire update path to current conference
        $currentConference = $app->getCurrentConference();
        if ($currentConference) {
            // Scope livewire update path to current serie
            $currentSerie = $app->getCurrentSerie();
            // if (isset($pathInfos[3]) && $currentSerie && $currentSerie->path === $pathInfos[3]) {
            //     Livewire::setUpdateRoute(
            //         fn ($handle) => Route::post($currentConference->path . '/series/' . $currentSerie->path . '/livewire/update', $handle)->middleware('web')
            //     );
            // } else {
            //     Livewire::setUpdateRoute(fn ($handle) => Route::post($currentConference->path . '/livewire/update', $handle)->middleware('web'));
            //     // dd(Route::getRoutes());
            // }
        }

        setPermissionsTeamId($app->getCurrentConferenceId());


        return $next($request);
    }
}
