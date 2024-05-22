<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Submission;
use App\Panel\Conference\Livewire\Workflows\Classes\StageManager;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Illuminate\Support\Str;

class SubmissionDetail extends Page
{
    protected static string $view = 'frontend.conference.pages.submission-detail';

    public Submission $submission;

    public function mount(int $submissionId)
    {
        $this->submission = Submission::find($submissionId);
        
        if (!$this->canAccess()) {
            abort(404);
        }
    }

    public function canAccess(): bool
    {
        return $this->submission->proceeding && 
        (
            (StageManager::editing()->isStageOpen() && auth()->user()->can('editing', $this->submission)) || 
            ($this->submission?->isPublished())
        );
    }

    public function canPreview(): bool
    {
        return ! $this->submission->proceeding?->isPublished() ||
        (! $this->submission->isPublished() && StageManager::editing()->isStageOpen() && auth()->user()->can('editing', $this->submission));
    }

    public function getBreadcrumbs(): array
    {
        return [
            route(Home::getRouteName()) => 'Home',
            route(Proceedings::getRouteName()) => 'Proceedings',
            route(ProceedingDetail::getRouteName(), [$this->submission->proceeding->id]) => Str::limit(
                $this->submission->proceeding->seriesTitle(), 70
            ),
            'Article'
        ];
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get("/{$slug}/{submissionId?}", static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
