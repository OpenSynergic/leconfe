<?php

namespace App\Frontend\Conference\Pages;

use App\Frontend\Conference\Pages\Proceeding as PagesProceeding;
use App\Models\Proceeding;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class ProceedingDetail extends Page
{
    protected static string $view = 'frontend.conference.pages.proceeding-detail';

    public Proceeding $proceeding;

    public function mount(int $proceedingId)
    {
        $this->proceeding = Proceeding::find($proceedingId);

        abort_if(! $this->proceeding, 404);
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/') => 'Home',
            route(PagesProceeding::getRouteName()) => 'Proceeding',
            $this->proceeding->seriesTitle(),
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get("/proceeding/view/{proceedingId}", static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }

    public function getViewData(): array
    {
        return [
            'proceeding' => $this->proceeding,
            'articles' => $this->proceeding->submissions()->get(),
        ];
    }
}
