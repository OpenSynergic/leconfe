<?php

namespace App\Conference\Pages;

use App\Models\Announcement;
use App\Models\Enums\ContentType;
use App\Models\StaticPage as ModelsStaticPage;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class AnnouncementPageList extends Page
{
    protected static string $view = 'conference.pages.announcement-list';

    public function mount()
    {
        //
    }

    protected function getViewData(): array
    {
        $announcementList = Announcement::whereMeta('expires_at', '>', now())->orderBy('created_at', 'desc')->get();
        $curentDate = today();
        
        return [
            'currentDate' => $curentDate,
            'announcementList' => $announcementList,
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
}
