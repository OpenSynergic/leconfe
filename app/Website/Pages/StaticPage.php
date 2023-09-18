<?php

namespace App\Website\Pages;

use App\Models\StaticPage as ModelsStaticPage;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class StaticPage extends Page
{
    protected static string $view = 'website.pages.static-page';

    public function mount($slug)
    {
        static::$slug = $slug;
    }

    public function getRecordProperty()
    {
        return ModelsStaticPage::where('slug', static::$slug)->first();
    }

    protected function getViewData(): array
    {
        return [
            //
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();

        Route::get('page/{slug}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
