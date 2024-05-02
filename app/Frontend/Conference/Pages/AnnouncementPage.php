<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Announcement;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class AnnouncementPage extends Page
{
    protected static string $view = 'frontend.conference.pages.announcement';

    public Announcement $announcement;

    public function mount()
    {
    }

    protected function getViewData(): array
    {
        return [];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();

        Route::get('announcements/{announcement}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
