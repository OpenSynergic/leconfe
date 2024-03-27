<?php

namespace App\Providers;

use App\Http\Middleware\IdentifyConference;
use App\Http\Middleware\SetupDefaultData;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroup;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->resolving('livewire-page-group', function () {
            LivewirePageGroup::registerPageGroup(
                $this->websitePageGroup(PageGroup::make()),
            );
            LivewirePageGroup::registerPageGroup(
                $this->conferencePageGroup(PageGroup::make()),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/frontend/website/components'), 'website');
    }

    public function websitePageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->id('website')
            ->path('')
            ->layout('frontend.website.components.layouts.app')
            ->bootUsing(function () {

            })
            ->middleware([
                'web',
                SetupDefaultData::class,
            ], true)
            ->discoverPages(in: app_path('Frontend/Website/Pages'), for: 'App\\Frontend\\Website\\Pages');
    }

    public function conferencePageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->id('conference')
            ->path('{conference:path}')
            ->middleware([
                'web',
                SetupDefaultData::class,
            ], true)
            ->layout('frontend.website.components.layouts.app')
            ->bootUsing(function () {

            })
            ->middleware([
                'web',
                IdentifyConference::class,
                SetupDefaultData::class,
            ], true)
            ->discoverPages(in: app_path('Frontend/Conference/Pages'), for: 'App\\Frontend\\Conference\\Pages');
    }
}
