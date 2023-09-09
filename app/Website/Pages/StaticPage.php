<?php

namespace App\Website\Pages;

use App\Models\Announcement;
use App\Models\Enums\ContentType;
use App\Models\StaticPage as ModelsStaticPage;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class StaticPage extends Page
{
    protected static string $view = 'website.pages.static-page';

    public function mount()
    {
        //
    }

    protected function getViewData(): array
    {
        $currentStaticPage = ModelsStaticPage::whereMeta('path', Route::current()->parameter('path'))->first();

        return [
            'currentStaticPage' => $currentStaticPage
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();

        Route::get('page/{path}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
