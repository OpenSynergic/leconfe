<?php

namespace App\Frontend\Conference\Pages;

use App\Frontend\Conference\Pages\Proceedings as PagesProceedings;
use App\Models\Proceeding;
use App\Panel\Conference\Livewire\Workflows\Classes\StageManager;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Illuminate\Support\Str;

class ProceedingDetail extends Page
{
    protected static string $view = 'frontend.conference.pages.proceeding-detail';

    public Proceeding $proceeding;

    public function mount(Proceeding $proceeding)
    {
        abort_unless($this->canAccess(), 404);
    }

    public function canAccess(): bool
    {
        return StageManager::editing()->isStageOpen() || $this->proceeding->isPublished();
    }

    public function canPreview(): bool
    {
        return ! $this->proceeding->isPublished() && StageManager::editing()->isStageOpen();
    }

    public function getBreadcrumbs(): array
    {
        return [
            route(Home::getRouteName()) => 'Home',
            route(PagesProceedings::getRouteName()) => 'Proceedings',
            Str::limit($this->proceeding->seriesTitle(), 120),
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get("/proceedings/view/{proceeding}", static::class)
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
