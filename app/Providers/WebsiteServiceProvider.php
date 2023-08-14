<?php

namespace App\Providers;

use App\Website\Pages\Home;
use Illuminate\Support\Facades\Blade;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroupServiceProvider;

class WebsiteServiceProvider extends PageGroupServiceProvider
{
    public function pageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->id('website')
            ->path('')
            ->layout('website.layouts.app')
            ->homePage(Home::class)
            ->discoverPages(in: app_path('Website/Pages'), for: 'App\\Website\\Pages');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/website/components'), 'website');
    }
}
