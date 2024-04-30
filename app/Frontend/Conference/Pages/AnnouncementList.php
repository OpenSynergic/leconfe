<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Announcement;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class AnnouncementList extends Page
{
    protected static string $view = 'frontend.conference.pages.announcement-list';

    protected function getViewData(): array
    {
        return [
            'announcements' => Announcement::query()
                ->whereMeta('expires_at', '>', now()->startOfDay())
                ->orWhereMeta('expires_at', '')
                ->orderBy('created_at', 'desc')
                ->with([
                    'tags' => function ($query) {
                        $query->take(3);
                    },
                    'user',
                ])
                ->withCount('tags')
                ->get(),
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();

        Route::get('announcements', static::class)
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
