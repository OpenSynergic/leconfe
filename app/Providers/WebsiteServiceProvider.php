<?php

namespace App\Providers;

use App\Managers\BlockManager;
use App\Facades\Block;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\Website\ApplyCurrentConference;
use App\Http\Middleware\IdentifyCurrentConference;
use App\Website\Blocks\ExampleBlock;
use App\Website\Pages\Home;
use Illuminate\Support\Facades\Blade;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroupServiceProvider;

class WebsiteServiceProvider extends PageGroupServiceProvider
{
    public function register()
    {
        parent::register();

        // Register blocks
        Block::registerBlocks([
            ExampleBlock::class,
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
