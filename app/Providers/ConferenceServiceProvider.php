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
use App\Models\Conference;
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
        // This isn't a good way, problem maybe caught up after new pages add to the project
        if (!in_array(request()->segment(1), ['administration', 'phpmyinfo'])) {
            $this->app->resolving('livewire-page-group', function () {
                LivewirePageGroup::registerPageGroup($this->conference(PageGroup::make()));
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (!app()->runningInConsole() && app()->isInstalled()) {
            $this->detectConference();

            // Scope livewire update path tu current conference
            $currentConference = app()->getCurrentConference();
            if ($currentConference) {
                Livewire::setUpdateRoute(function ($handle) use ($currentConference) {
                    return Route::post($currentConference->path . '/panel/livewire/update', $handle)
                        ->middleware('web');
                });
            }
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

    protected function conference(PageGroup $pageGroup): PageGroup
    {
        return $this->setupPageGroup($pageGroup)
            ->id('conference')
            ->path('{conference:path}')
            ->middleware([
                IdentifyCurrentConference::class,
                SetupDefaultData::class,
            ], true);
    }

    protected function detectConference()
    {
        if (!app()->isInstalled()) {
            return;
        }

        $pathInfos = explode('/', request()->getPathInfo());

        // Special case for `current` path
        if (isset($pathInfos[1]) && !blank($pathInfos[1])) {
            $conferenceId = DB::table('conferences')->where('path', $pathInfos[1])->value('id');
            if (!$conferenceId) {
                // Conference not found
                app()->setCurrentConferenceId(0);
                return;
            }

            app()->setCurrentConferenceId($conferenceId);
            app()->scopeCurrentConference();

            return;
        }

        return;
    }
}
