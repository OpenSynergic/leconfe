<?php

namespace App\Providers;

use App\Conference\Pages\Home;
use App\Http\Middleware\IdentifyArchiveConference;
use App\Http\Middleware\IdentifyCurrentConference;
use App\Http\Middleware\SetupDefaultData;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroup;

class ConferenceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->resolving('livewire-page-group', function () {
            LivewirePageGroup::registerPageGroup($this->currentConference(PageGroup::make()));
            LivewirePageGroup::registerPageGroup($this->archiveConference(PageGroup::make()));
        });
    }

    /**
     * Bootstrap services.
     */
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/conference/components'), 'conference');

        Livewire::addPersistentMiddleware([
            IdentifyCurrentConference::class,
            SetupDefaultData::class,
        ]);
    }

    protected function currentConference(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->id('current-conference')
            ->path('current')
            ->layout('website.components.layouts.app')
            ->homePage(Home::class)
            ->middleware([
                'web',
                IdentifyCurrentConference::class,
                SetupDefaultData::class,
            ], true)
            ->discoverPages(in: app_path('Conference/Pages'), for: 'App\\Conference\\Pages');
    }

    protected function archiveConference(PageGroup $pageGroup): PageGroup
    {
        return $this->currentConference($pageGroup)
            ->id('archive-conference')
            ->middleware([
                IdentifyArchiveConference::class,
            ], true)
            ->path('archive/{conference}');
    }

    protected function upcomingConference(PageGroup $pageGroup): PageGroup
    {
        return $this->currentConference($pageGroup)
            ->id('upcoming-conference')
            ->middleware([
                IdentifyArchiveConference::class,
            ], true)
            ->path('archive/{conference}');
    }
}
