<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Proceeding;
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

    public Collection $proceedings;

    public function mount()
    {
        $this->proceedings = Proceeding::query()
            ->published()
            ->orderBy('order_column')
            ->get();
    }

    public function getBreadcrumbs(): array
    {
        return [
            route(Home::getRouteName()) => 'Home',
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
