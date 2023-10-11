<?php

namespace App\Providers;

use App\Facades\Block;
use App\Http\Middleware\SetupDefaultData;
use App\Website\Blocks\ExampleBlock;
use App\Website\Blocks\LeftBlock;
use App\Website\Pages\Home;
use Illuminate\Support\Facades\Blade;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroupServiceProvider;

class WebsiteServiceProvider extends PageGroupServiceProvider
{
    public function register()
    {
        parent::register();
        Block::registerBlocks([
            ExampleBlock::class,
            LeftBlock::class,
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
                    ExampleBlock::class,
                    LeftBlock::class,
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
