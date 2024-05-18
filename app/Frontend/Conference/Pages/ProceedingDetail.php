<?php

namespace App\Frontend\Conference\Pages;

use App\Frontend\Conference\Pages\Proceedings as PagesProceedings;
use App\Models\Proceeding;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Illuminate\Support\Str;

class ProceedingDetail extends Page
{
    protected static string $view = 'frontend.conference.pages.proceeding-detail';

    public Proceeding $proceeding;

    public function mount(int $proceedingId)
    {
        $this->proceeding = Proceeding::find($proceedingId);

        if (! $this->proceeding || ! $this->proceeding->isPublished()) {
            abort(404);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/') => 'Home',
            route(PagesProceedings::getRouteName()) => 'Proceedings',
            Str::limit($this->proceeding->seriesTitle(), 120),
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get("/proceedings/view/{proceedingId}", static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }

    public function getViewData(): array
    {
        return [
            'proceeding' => $this->proceeding,
            'articles' => $this->proceeding->submissions()->published(),
        ];
    }
}
