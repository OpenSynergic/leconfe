<?php

namespace App\Providers;

use App\Facades\Block;
use Livewire\Livewire;
use App\Conference\Pages\Home;
use App\Conference\Blocks\MenuBlock;
use App\Conference\Blocks\TopicBlock;
use Illuminate\Support\Facades\Blade;
use App\Conference\Blocks\SubmitBlock;
use Illuminate\Support\ServiceProvider;
use App\Conference\Blocks\CalendarBlock;
use App\Conference\Blocks\PreviousBlock;
use App\Conference\Blocks\TimelineBlock;
use App\Conference\Blocks\CommitteeBlock;
use App\Http\Middleware\CountTotalVisits;
use App\Http\Middleware\SetupDefaultData;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use App\Http\Middleware\IdentifyArchiveConference;
use App\Http\Middleware\IdentifyCurrentConference;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;

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
            ->bootUsing(function () {
                Block::registerBlocks([
                    CalendarBlock::class,
                    TimelineBlock::class,
                    PreviousBlock::class,
                    SubmitBlock::class,
                    TopicBlock::class,
                    MenuBlock::class,
                    CommitteeBlock::class,
                ]);
                Block::boot();
            })
            ->middleware([
                'web',
                IdentifyCurrentConference::class,
                CountTotalVisits::class,
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
                CountTotalVisits::class,
            ], true)
            ->path('archive/{conference}');
    }

    protected function upcomingConference(PageGroup $pageGroup): PageGroup
    {
        return $this->currentConference($pageGroup)
            ->id('upcoming-conference')
            ->middleware([
                IdentifyArchiveConference::class,
                CountTotalVisits::class,
            ], true)
            ->path('archive/{conference}');
    }
}
