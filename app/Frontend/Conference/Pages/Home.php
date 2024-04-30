<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Announcement;
use App\Models\SpeakerRole;
use App\Models\Topic;
use App\Models\Venue;
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
        return [
            'announcements' => Announcement::query()->get(),
            'participantPosition' => SpeakerRole::query()
                ->whereHas('speakers')
                ->with(['speakers' => ['media', 'meta']])
                ->get(),
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

    protected function getLayoutData(): array
    {
        return [
            'title' => $this->getTitle()
        ];
    }
}
