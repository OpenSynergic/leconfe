<?php

namespace App\Conference\Pages;

use App\Models\Announcement;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class AnnouncementPage extends Page
{
    protected static string $view = 'conference.pages.announcement';

    public string $id;

    public function mount()
    {
        //
    }

    public function getRecordProperty()
    {
        return Announcement::where('id', $this->id)->first();
    }

    protected function getViewData(): array
    {
        return [];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();

        Route::get('announcements/{id}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
