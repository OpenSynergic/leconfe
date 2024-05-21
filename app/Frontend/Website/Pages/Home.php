<?php

namespace App\Frontend\Website\Pages;

use App\Facades\Block as BlockFacade;
use App\Facades\SidebarFacade;
use App\Models\Conference;
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
            ->with(['conference', 'media']);
        
        $currentSeries = (clone $serieQuery)->active()->paginate(6, pageName: 'currentSeriesPage');

        return [
            'currentSeries' => $currentSeries,
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
