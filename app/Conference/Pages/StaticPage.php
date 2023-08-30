<?php

namespace App\Conference\Pages;

use App\Models\UserContent;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class StaticPage extends Page
{
    protected static string $view;

    public function mount()
    {

        // dd(Route::currentRouteName());
        switch (Route::currentRouteName()) {
            case 'livewirePageGroup.current-conference.pages.static-page':
                static::$view = 'conference.pages.static-page';
                break;
            
            default:
                static::$view = 'conference.pages.static-page-list';
                break;
        }
    }

    protected function getViewData(): array
    {
        switch (Route::currentRouteName()) {
            case 'livewirePageGroup.current-conference.pages.static-page':
                $currentStaticPage = UserContent::where('id', Route::current()->parameter('user_content'))->first();
                return [
                    'currentStaticPage' => $currentStaticPage
                ];
                break;
            
            default:
                $contentTypeSlug = Route::current()->parameter('content_type');
                $contentType = ucfirst(str_replace('-', '', ucwords($contentTypeSlug, '-')));

                // here will check if content_type is in ContentType::class. if not return 404;

                $staticPageList = UserContent::where('content_type', $contentType)->get();
                return [
                    'contentTypeSlug' => $contentTypeSlug,
                    'staticPageList' => $staticPageList,
                ];
                break;
        }
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();

        Route::get('{content_type}/{user_content}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
        Route::get('{content_type}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str('static-page-list')->replace('/', '.'));
    }
}
