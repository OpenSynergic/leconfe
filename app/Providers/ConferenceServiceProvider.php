<?php

namespace App\Providers;

use App\Conference\Blocks\CalendarBlock;
use App\Conference\Blocks\CommitteeBlock;
use App\Conference\Blocks\PreviousBlock;
use App\Conference\Blocks\SubmitBlock;
use App\Conference\Blocks\TimelineBlock;
use App\Conference\Blocks\TopicBlock;
use App\Conference\Pages\Home;
use App\Facades\Block;
use App\Http\Middleware\BootPluginMiddleware;
use App\Http\Middleware\IdentifyArchiveConference;
use App\Http\Middleware\IdentifyCurrentConference;
use App\Http\Middleware\SetupDefaultData;
use App\Models\Enums\ConferenceStatus;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
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

        $this->detectConference();
    }

    /**
     * Bootstrap services.
     */
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade::anonymousComponentPath(resource_path('views/conference/components'), 'conference');

        // Livewire::addPersistentMiddleware([
        //     IdentifyCurrentConference::class,
        //     SetupDefaultData::class,
        // ]);
    }

    protected function setupPageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->layout('website.components.layouts.app')
            ->homePage(Home::class)
            ->bootUsing(function () {
                Block::registerBlocks([
                    CalendarBlock::class,
                    TimelineBlock::class,
                    PreviousBlock::class,
                    SubmitBlock::class,
                    TopicBlock::class,
                    CommitteeBlock::class,
                ]);
                Block::boot();
            })
            ->middleware([
                'web',
            ], true)
            ->discoverPages(in: app_path('Conference/Pages'), for: 'App\\Conference\\Pages');
    }

    protected function currentConference(PageGroup $pageGroup): PageGroup
    {
        return $this->setupPageGroup($pageGroup)
            ->id('current-conference')
            ->path('current')
            ->middleware([
                // IdentifyCurrentConference::class,
                SetupDefaultData::class,
                // BootPluginMiddleware::class,
            ], true);
    }

    protected function archiveConference(PageGroup $pageGroup): PageGroup
    {
        return $this->setupPageGroup($pageGroup)
            ->id('archive-conference')
            ->middleware([
                // IdentifyArchiveConference::class,
                SetupDefaultData::class,
                // BootPluginMiddleware::class,
            ], true)
            ->path('archive/{conference}');
    }

    protected function upcomingConference(PageGroup $pageGroup): PageGroup
    {
        return $this->setupPageGroup($pageGroup)
            ->id('upcoming-conference')
            ->middleware([
                // IdentifyArchiveConference::class,
                SetupDefaultData::class,
                // BootPluginMiddleware::class,
            ], true)
            ->path('archive/{conference}');
    }

    protected function detectConference()
    {
        $pathInfos = explode('/', request()->getPathInfo());

        // Special case for `current` path
        if(isset($pathInfos[1]) && $pathInfos[1] === 'current'){
            $conferenceId = DB::table('conferences')->where('status', ConferenceStatus::Active->value)->value('id');
            if($conferenceId){
                app()->setCurrentConferenceId($conferenceId);
                app()->scopeCurrentConference();
                return;
            }
        }


        if(!isset($pathInfos[2])){
            app()->setCurrentConferenceId(0);
            return;
        }
        
        
        $conferencePath = $pathInfos[2];
        $conferenceId   = DB::table('conferences')->where('path', $conferencePath)->value('id');

        if(!$conferenceId){
            app()->setCurrentConferenceId(0);
            return;
        }

        app()->setCurrentConferenceId($conferenceId);
        app()->scopeCurrentConference();
    }
}
