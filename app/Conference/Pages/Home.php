<?php

namespace App\Conference\Pages;

use App\Models\Announcement;
use App\Models\ParticipantPosition;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Home extends Page
{
    protected static string $view = 'conference.pages.home';

    protected function getViewData(): array
    {
        $filteredPositions = $this->getFilteredParticipantPositions();
        $announcements = $this->getAnnouncements();

        return [
            'announcements' => $announcements,
            'participantPosition' => $filteredPositions,
        ];
    }

    protected function getFilteredParticipantPositions()
    {
        return ParticipantPosition::query()
            ->whereHas('participants')
            ->with(['participants' => ['media', 'meta']])
            ->get();
    }

    protected function getAnnouncements()
    {
        return Announcement::query()->get();
    }

    public function mount()
    {
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
