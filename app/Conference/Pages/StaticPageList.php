<?php

namespace App\Conference\Pages;

use App\Models\Announcement;
use App\Models\Enums\ContentType;
use App\Models\StaticPage as ModelsStaticPage;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class StaticPageList extends Page
{
    protected static string $view = 'conference.pages.static-page-list';

    public function mount()
    {
        //
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

        $staticPageList = $userContent::whereMeta('expires_at', '>', now())->get();
        
        return [
            'contentTitle' => $contentTitle,
            'contentTypeSlug' => $contentTypeSlug,
            'staticPageList' => $staticPageList,
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();

        Route::get('{content_type}', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
