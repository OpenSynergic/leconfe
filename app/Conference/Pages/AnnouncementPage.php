<?php

namespace App\Conference\Pages;

use App\Models\Announcement;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Webwizo\Shortcodes\Facades\Shortcode;

class AnnouncementPage extends Page
{
    protected static string $view = 'conference.pages.announcement';

    public Announcement $announcement;

    public function mount()
    {
        Shortcode::enable();
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
