<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs\Tab as Tab;
use App\Infolists\Components\VerticalTabs\Tabs as Tabs;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\UserRole;
use App\Panel\Livewire\Submissions\CallforAbstract;
use App\Panel\Livewire\Submissions\PeerReview;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use App\Panel\Resources\SubmissionResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs as HorizontalTabs;
use Filament\Infolists\Components\Tabs\Tab as HorizontalTab;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewSubmission extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists, InteractsWithForms, InteractsWithRecord, InteractWithTenant;

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.view-submission';

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->resolveRecord($record);

        // The person reviewing this submission cannot open the page with the details of the submission.
        abort_if(
            $this->record->reviews()->where('user_id', auth()->id())->exists(),
            403
        );

        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function getHeading(): string
    {
        return $this->record->getMeta('title');
    }

    /**
     * Question:
     * Do we really need this (?)
     */
    // public function getSubheading(): string|Htmlable|null
    // {
    //     return new HtmlString("<span class='text-danger-600 text-base'>Declined</span>");
    // }

    public function infolist(Infolist $infolist): Infolist
    {
        $currentUserIsReviewer = auth()->user()->hasRole(UserRole::Reviewer->value);
        return $infolist
            ->schema([
                HorizontalTabs::make()
                    ->contained(false)
                    ->tabs([
                        HorizontalTab::make('Workflow')
                            ->schema([
                                Tabs::make()
                                    ->sticky()
                                    ->tabs([
                                        Tab::make("Call for Abstract")
                                            ->hidden($currentUserIsReviewer)
                                            ->icon("heroicon-o-information-circle")
                                            ->schema([
                                                LivewireEntry::make('call-for-abstract')
                                                    ->livewire(CallforAbstract::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ]),
                                        Tab::make("Peer Review")
                                            ->visible(
                                                fn (): bool => $this->conference->getMeta('workflow.peer-review.open', false)
                                            )
                                            ->icon("iconpark-checklist-o")
                                            ->schema([
                                                LivewireEntry::make('peer-review')
                                                    ->livewire(PeerReview::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ]),
                                        Tab::make("Editing")
                                            ->visible(fn (): bool => $this->conference->getMeta('workflow.editing.open', false))
                                            ->icon("heroicon-o-pencil")
                                    ])
                                    ->maxWidth('full')
                            ]),
                        HorizontalTab::make('Publication')
                            ->schema([
                                Tabs::make()
                                    ->tabs([
                                        Tab::make('Detail')
                                            ->icon("heroicon-o-information-circle")
                                            ->schema([]),
                                        Tab::make('Authors')
                                            ->icon("heroicon-o-user-group")
                                            ->schema([]),
                                        Tab::make('References')
                                            ->icon("iconpark-list")
                                            ->schema([]),
                                    ])
                            ])
                    ])
            ]);
    }

    public function getTitle(): string
    {
        return $this->record->stage == SubmissionStage::Wizard ? 'Submission Wizard' : 'Submission';
    }
}
