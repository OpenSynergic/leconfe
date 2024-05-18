<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Proceeding as ModelsProceeding;
use App\Models\Topic;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Collection;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Proceedings extends Page
{
    protected static string $view = 'frontend.conference.pages.proceedings';

    protected static ?string $slug = 'proceedings';

    public Collection $topics, $proceedings;

    public function mount(?string $topicSlug = null)
    {
        $this->topics = filled($topicSlug)
            ? Topic::whereSlug($topicSlug)->get()
            : Topic::whereHas('submissions')->get();

        $this->proceedings = ModelsProceeding::query()
            ->published()
            ->orderBy('order_column')
            ->get();
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/') => 'Home',
            'Proceedings',
        ];
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
