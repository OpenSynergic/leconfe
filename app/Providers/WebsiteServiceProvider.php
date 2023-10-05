<?php

namespace App\Providers;

use App\Facades\Block;
use App\Conference\Pages\Home;
use App\Http\Middleware\SetupDefaultData;
use App\Website\Blocks\ExampleBlock;
use App\Website\Blocks\LeftBlock;
use App\Website\Blocks\LoginBlock;

use App\Website\Blocks\SearchBlock;
use App\Conference\Blocks\MenuBlock;
use App\Conference\Blocks\TopicBlock;
use App\Website\Blocks\ScheduleBlock;
use Illuminate\Support\Facades\Blade;
use App\Conference\Blocks\SubmitBlock;
use App\Conference\Blocks\CalendarBlock;
use App\Conference\Blocks\TimelineBlock;
use App\Http\Middleware\DefaultViewData;
use App\Website\Blocks\InformationBlock;
use App\Conference\Blocks\EditorialBlock;
use App\Conference\Blocks\PreviousBlock;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use App\Http\Middleware\IdentifyCurrentConference;
use Rahmanramsi\LivewirePageGroup\PageGroupServiceProvider;

class WebsiteServiceProvider extends PageGroupServiceProvider
{
    public function register()
    {
        parent::register();
        Block::registerBlocks([
            CalendarBlock::class,
            TimelineBlock::class,
            PreviousBlock::class,
            SubmitBlock::class,
            TopicBlock::class,
            MenuBlock::class,
            EditorialBlock::class
        ]);
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
