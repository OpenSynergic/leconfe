<?php

namespace App\Providers;

use App\Facades\Plugin;
use App\Http\Middleware\IdentifyConference;
use App\Http\Middleware\IdentifyScheduledConference;
use App\Http\Middleware\RestoreLivewireConferenceContext;
use App\Http\Middleware\SetLocale;
use App\Http\Responses\Auth\LogoutResponse;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
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
            LivewirePageGroup::registerPageGroup(
                $this->scheduledConferencePageGroup(PageGroup::make()),
            );

            Livewire::addPersistentMiddleware([
                'web',
                RestoreLivewireConferenceContext::class,
                SetLocale::class,
            ]);

        });

        $this->app->bind(\Filament\Auth\Http\Responses\Contracts\LogoutResponse::class, LogoutResponse::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/frontend/website/components'), 'website');
        Blade::anonymousComponentPath(resource_path('views/frontend/conference/components'), 'conference');
        Blade::anonymousComponentPath(resource_path('views/frontend/scheduledConference/components'), 'scheduledConference');
    }

    public function websitePageGroup(PageGroup $pageGroup): PageGroup
    {
        $pageGroup
            ->id('website')
            ->path('')
            ->layout('frontend.website.components.layouts.app')
            ->middleware([
                'web',
                RestoreLivewireConferenceContext::class,
            ], true)
            ->discoverPages(in: app_path('Frontend/Website/Pages'), for: 'App\\Frontend\\Website\\Pages');

        Plugin::getPluginsForRegistration('site')->each(fn ($plugin) => $plugin->onFrontend($pageGroup));

        return $pageGroup;
    }

    public function conferencePageGroup(PageGroup $pageGroup): PageGroup
    {
        $pageGroup
            ->id('conference')
            ->path('{conference:path}')
            ->layout('frontend.website.components.layouts.app')
            ->middleware([
                'web',
                RestoreLivewireConferenceContext::class,
                IdentifyConference::class,
            ], true)
            ->discoverPages(in: app_path('Frontend/Conference/Pages'), for: 'App\\Frontend\\Conference\\Pages');

        Plugin::getPluginsForRegistration('conference')->each(fn ($plugin) => $plugin->onFrontend($pageGroup));

        return $pageGroup;
    }

    public function scheduledConferencePageGroup(PageGroup $pageGroup): PageGroup
    {
        $pageGroup
            ->id('scheduledConference')
            ->path('{conference:path}/scheduled/{serie:path}')
            ->layout('frontend.website.components.layouts.app')
            ->middleware([
                'web',
                RestoreLivewireConferenceContext::class,
                IdentifyConference::class,
                IdentifyScheduledConference::class,
            ], true)
            ->discoverPages(in: app_path('Frontend/ScheduledConference/Pages'), for: 'App\\Frontend\\ScheduledConference\\Pages');

        Plugin::getPluginsForRegistration('scheduled-conference')->each(fn ($plugin) => $plugin->onFrontend($pageGroup));

        return $pageGroup;
    }
}
