<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Topic;
use App\Models\Venue;
use App\Models\Conference;
use App\Models\Submission;
use App\Models\SpeakerRole;
use App\Models\Announcement;
use App\Models\CommitteeRole;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Home extends Page
{
    protected static string $view = 'frontend.conference.pages.home';

    public function mount()
    {
    }

    protected function getViewData(): array
    {
        $activeSeries = app()->getCurrentConference()->series()->active()->first();

        if ($activeSeries) {
            $committeePosition = CommitteeRole::byActiveSeries($activeSeries->id)->get();
            $participantPosition = SpeakerRole::byActiveSeries($activeSeries->id)->get();
        }

        return [
            'announcements' => Announcement::query()->get(),
            'committeePosition' => $committeePosition,
            'participantPosition' => $participantPosition,
            'acceptedSubmission' => app()->getCurrentConference()->submission()->published()->get(),
            'topics' => Topic::query()->get(),
            'venues' => Venue::query()->get(),
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get('/', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
