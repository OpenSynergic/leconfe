<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Announcement;
use App\Models\ParticipantPosition;
use App\Models\Topic;
use App\Models\Venue;
use Illuminate\Routing\Route as RoutingRoute;
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
            'participantPosition' => ParticipantPosition::query()
                ->where('type', 'speaker')
                ->whereHas('participants')
                ->with(['participants' => ['media', 'meta']])
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
}
