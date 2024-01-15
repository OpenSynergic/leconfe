<?php

namespace App\Providers;

use App\Conference\Pages\Home;
use App\Facades\Block;
use App\Http\Middleware\SetupDefaultData;
use App\Website\Blocks\CalendarBlock;
use App\Website\Blocks\LoginBlock;
use App\Website\Blocks\SearchBlock;
use App\Website\Blocks\TopicBlock;
use App\Website\Blocks\UpcomingConferenceBlock;
use Illuminate\Support\Facades\Blade;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroupServiceProvider;

class WebsiteServiceProvider extends PageGroupServiceProvider
{
    public function register()
    {
        parent::register();
    }

    public function pageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->id('website')
            ->path('')
            ->layout('website.components.layouts.app')
            ->homePage(Home::class)
            ->bootUsing(function () {
                app()->scopeCurrentConference();

                // Register blocks
                Block::registerBlocks([
                    SearchBlock::class,
                    LoginBlock::class,
                    CalendarBlock::class,
                    UpcomingConferenceBlock::class,
                    TopicBlock::class,
                ]);
                Block::boot();
            })
            ->middleware([
                'web',
                SetupDefaultData::class,
            ], true)
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
