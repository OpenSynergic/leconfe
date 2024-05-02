<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Topic;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Collection;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Proceeding extends Page
{
    protected static string $view = 'frontend.conference.pages.proceeding';

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
