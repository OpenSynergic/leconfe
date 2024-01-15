<?php

namespace App\Website\Pages;

use App\Models\Conference;
use App\Models\Topic;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Home extends Page
{
    protected static string $view = 'website.pages.home';

    protected function getViewData(): array
    {
        $activeConference = Conference::active();

        return [
            'topics' => Topic::withoutGlobalScopes()->where('conference_id', $activeConference->getKey())->get(),
            'upcomingConferences' => Conference::upcoming()->get(),
            'activeConference' => $activeConference,
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
