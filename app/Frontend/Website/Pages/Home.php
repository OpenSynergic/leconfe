<?php

namespace App\Frontend\Website\Pages;

use App\Facades\Block as BlockFacade;
use App\Facades\SidebarFacade;
use App\Models\Conference;
use App\Models\Enums\SerieState;
use App\Models\Serie;
use App\Models\Sponsor;
use App\Models\Topic;
use Illuminate\Support\Facades\Route;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Home extends Page
{
    use WithPagination, WithoutUrlPagination;

    protected static string $view = 'frontend.website.pages.home';


    protected function getViewData(): array
    {
        $serieQuery = Serie::query()
            ->withoutGlobalScopes()
            ->with(['conference', 'media', 'meta']);
        
        $currentSeries = (clone $serieQuery)
            ->state(SerieState::Current)
            ->paginate(6, pageName: 'currentSeriesPage');

        $upcomingSeries = (clone $serieQuery)
            ->state(SerieState::Published)
            ->paginate(6, pageName: 'upcomingSeriesPage');

        $allSeries = (clone $serieQuery)
            ->whereNot('state', SerieState::Draft)
            ->paginate(6, pageName: 'allSeriesPage');

        return [
            'currentSeries' => $currentSeries,
            'upcomingSeries' => $upcomingSeries,
            'allSeries' => $allSeries,
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get('/', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
