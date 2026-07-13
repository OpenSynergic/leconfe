<?php

namespace Rahmanramsi\LivewirePageGroup\Pages;

use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;

class HomePage extends Page
{
    protected static string $view = 'livewire-page-group::pages.home';

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get('/', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
