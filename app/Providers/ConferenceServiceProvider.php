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
use App\Http\Middleware\IdentifyArchiveConference;
use App\Http\Middleware\IdentifyCurrentConference;
use App\Http\Middleware\SetupDefaultData;
use App\Models\Enums\ConferenceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
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
    public function boot(): void
    {
        
        if(!app()->runningInConsole() && app()->isInstalled()){
            // Scope livewire update path tu current conference
            $currentConference = app()->getCurrentConference();
            if($currentConference){
                Livewire::setUpdateRoute(function ($handle) use  ($currentConference){
                    return Route::post('/livewire/' . $currentConference->path .  '/update', $handle)
                        ->middleware('web');
                });
            }
            return;
        }
    }

    protected function setupPageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->layout('website.components.layouts.app')
            ->homePage(Home::class)
            ->bootUsing(function () {
                Block::registerBlocks([
                    new CalendarBlock,
                    new TimelineBlock,
                    new PreviousBlock,
                    new SubmitBlock,
                    new TopicBlock,
                    new CommitteeBlock,
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
            ], true);
    }

    protected function archiveConference(PageGroup $pageGroup): PageGroup
    {
        return $this->setupPageGroup($pageGroup)
            ->id('archive-conference')
            ->middleware([
                IdentifyArchiveConference::class,
                SetupDefaultData::class,
            ], true)
            ->path('archive/{conference}');
    }

    protected function upcomingConference(PageGroup $pageGroup): PageGroup
    {
        return $this->setupPageGroup($pageGroup)
            ->id('upcoming-conference')
            ->middleware([
                IdentifyArchiveConference::class,
                SetupDefaultData::class,
            ], true)
            ->path('archive/{conference}');
    }

    protected function detectConference()
    {
        if(!app()->isInstalled()){
            return;
        }

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
