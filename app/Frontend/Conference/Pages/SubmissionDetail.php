<?php

namespace App\Frontend\Conference\Pages;

use App\Facades\MetaTag;
use App\Models\Submission;
use App\Panel\Conference\Livewire\Workflows\Classes\StageManager;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Illuminate\Support\Str;

class SubmissionDetail extends Page
{
    protected static string $view = 'frontend.conference.pages.submission-detail';

    public Submission $submission;

    public function mount(Submission $submission)
    {
        if (!$this->canAccess()) {
            abort(404);
        }

        $this->addMetadata();
    }

    public function getTitle(): string|Htmlable
    {
        return $this->submission->getMeta('title');
    }

    public function addMetadata() : void
    {
        
        MetaTag::add('citation_conference_title', app()->getCurrentConference()->name);
        MetaTag::add('citation_title', e($this->submission->getMeta('title')));

        $this->submission->authors->each(function ($author) {
            MetaTag::add('citation_author', $author->fullName);
            if($author->getMeta('affiliation')){
                MetaTag::add('citation_author_affiliation', $author->getMeta('affiliation'));
            }
        });

        if($this->submission->isPublished()){
            MetaTag::add('citation_publication_date', $this->submission->published_at?->format('Y/m/d'));
        }

        $proceeding = $this->submission->proceeding;
        MetaTag::add('citation_volume', $proceeding->volume);
        MetaTag::add('citation_issue', $proceeding->number);

        if($this->submission->getMeta('article_pages')){
            [$start, $end] = explode('-', $this->submission->getMeta('article_pages'));

            if($start){
                MetaTag::add('citation_firstpage', $start);
            }

            if($end){
                MetaTag::add('citation_lastpage', $end);
            }
        }

        MetaTag::add('citation_abstract_html_url', route(static::getRouteName(), ['submission' => $this->submission->getKey()]));
        
        $this->submission->galleys->each(function ($galley) {
            if($galley->isPdf()){
                MetaTag::add('citation_pdf_url', $galley->getUrl());
            }
        });

    }

    public function canAccess(): bool
    {
        if (!$this->submission->proceeding) {
            return false;
        }

        if (StageManager::editing()->isStageOpen() && auth()->user()?->can('editing', $this->submission)) {
            return true;
        }

        if ($this->submission->isPublished() && $this->submission->proceeding->isPublished()) {
            return true;
        }

        return false;
    }

    public function canPreview(): bool
    {
        if (!$this->submission->proceeding?->isPublished()) {
            return true;
        }

        $isSubmissionNotPublished = !$this->submission->isPublished();

        $isEditingStageOpen = StageManager::editing()->isStageOpen();

        $canUserEdit = auth()->user()?->can('editing', $this->submission);

        if ($isSubmissionNotPublished && $isEditingStageOpen && $canUserEdit) {
            return true;
        }

        return false;
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
        Route::get("/submission/{submission}", static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
