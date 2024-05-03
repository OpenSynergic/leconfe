<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Submission;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class SubmissionDetail extends Page
{
    protected static string $view = 'frontend.conference.pages.submission-detail';

    public Submission $submission;

    public function mount(int $submissionId)
    {
        $this->submission = Submission::find($submissionId);
        abort_if(! $this->submission, 404);
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
