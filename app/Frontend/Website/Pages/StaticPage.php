<?php

namespace App\Frontend\Website\Pages;

use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use App\Models\StaticPage as StaticPageModel;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class StaticPage extends Page
{
    protected static string $view = 'frontend.website.pages.static-page';

    public StaticPageModel $staticPage;

    public function mount()
    {
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

        Route::get('page/{staticPage:slug}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
