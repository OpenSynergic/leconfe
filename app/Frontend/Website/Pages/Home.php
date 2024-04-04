<?php

namespace App\Frontend\Website\Pages;

use App\Models\Conference;
use App\Models\Sponsor;
use App\Models\Topic;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Home extends Page
{
    protected static string $view = 'frontend.website.pages.home';

    protected function getViewData(): array
    {
        return [
            'sponsors' => Sponsor::ordered()->with('media')->get(),
            'currentConferences' => Conference::active()->with('media')->get(),
            'upcomingConferences' => Conference::upcoming()->with('media')->get(),
            'allConferences' => Conference::with('media')->get(),
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
