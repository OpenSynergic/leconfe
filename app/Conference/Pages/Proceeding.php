<?php

namespace App\Conference\Pages;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Proceeding extends Page
{
    protected static string $view = 'conference.pages.proceeding';

    public Collection $topics;

    public function mount(?string $topicSlug = null)
    {

        $this->topics = filled($topicSlug)
            ? Topic::whereSlug($topicSlug)->get()
            : Topic::whereHas('submissions')->get();
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get("/{$slug}/{topicSlug?}", static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
