<?php

namespace App\Conference\Pages;

use App\Models\Announcement;
use App\Models\Enums\ContentType;
use App\Models\StaticPage as ModelsStaticPage;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class AnnouncementPage extends Page
{
    protected static string $view = 'conference.pages.announcement';

    public function mount()
    {
        //
    }

    protected function getViewData(): array
    {
        $currentStaticPage = Announcement::where('id', Route::current()->parameter('user_content'))->first();

        return [
            'currentStaticPage' => $currentStaticPage
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();

        Route::get('announcements/{user_content}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
