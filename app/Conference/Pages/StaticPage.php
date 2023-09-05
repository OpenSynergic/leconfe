<?php

namespace App\Conference\Pages;

use App\Models\Announcement;
use App\Models\Enums\ContentType;
use App\Models\StaticPage as ModelsStaticPage;
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
        $contentTypeSlug = Route::current()->parameter('content_type');
        $contentType = ucfirst(str_replace('-', '', ucwords($contentTypeSlug, '-')));
        $contentTitle = ucfirst(str_replace('-', ' ', ucwords($contentTypeSlug, '-')));

        switch ($contentType) {
            case ContentType::Announcement->value:
                $userContent = Announcement::class;
                break;
            
            case ContentType::StaticPage->value:
                $userContent = ModelsStaticPage::class;
                break;
            
            default:
                break;
        }

        switch (Route::currentRouteName()) {
            case 'livewirePageGroup.current-conference.pages.static-page':
                $currentStaticPage = $userContent::where('id', Route::current()->parameter('user_content'))->first();
                return [
                    'currentStaticPage' => $currentStaticPage
                ];
                break;
            
            default:
                $staticPageList = $userContent::whereMeta('expires_at', '>', now())->get();
                return [
                    'contentTitle' => $contentTitle,
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
