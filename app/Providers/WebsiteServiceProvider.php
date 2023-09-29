<?php

namespace App\Providers;

use App\Facades\Block;
use App\Website\Pages\Home;
use App\Managers\BlockManager;
use App\Website\Blocks\LeftBlock;
use App\Website\Blocks\ExampleBlock;
use App\Website\Blocks\CalendarBlock;
use Illuminate\Support\Facades\Blade;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use App\Http\Middleware\IdentifyCurrentConference;
use App\Website\Blocks\EditorialBlock;
use App\Website\Blocks\InformationBlock;
use App\Website\Blocks\LoginBlock;
use App\Website\Blocks\ScheduleBlock;
use App\Website\Blocks\SearchBlock;
use App\Website\Blocks\SubmitBlock;
use App\Website\Blocks\TimelineBlock;
use App\Website\Blocks\TopicBlock;
use Rahmanramsi\LivewirePageGroup\PageGroupServiceProvider;

class WebsiteServiceProvider extends PageGroupServiceProvider
{
    public function register()
    {
        parent::register();

        // Register blocks
        Block::registerBlocks([
            // ExampleBlock::class,
            // LeftBlock::class,
            CalendarBlock::class,
            TimelineBlock::class,
            SearchBlock::class,
            LoginBlock::class,
            TopicBlock::class,
            InformationBlock::class,
            SubmitBlock::class,
            EditorialBlock::class
        ]);
    }

    public function pageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->id('website')
            ->path('')
            ->layout('conference.components.layouts.app')
            ->homePage(Home::class)
            ->bootUsing(function () {
                BlockManager::boot();
            })
            ->middleware([
                'web',
                IdentifyCurrentConference::class,
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
