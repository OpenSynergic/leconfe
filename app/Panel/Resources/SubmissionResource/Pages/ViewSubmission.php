<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs\Tab as Tab;
use App\Infolists\Components\VerticalTabs\Tabs as Tabs;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Panel\Livewire\Submissions\CallforAbstract;
use App\Panel\Livewire\Submissions\Components\ContributorList;
use App\Panel\Livewire\Submissions\Editing;
use App\Panel\Livewire\Submissions\Forms\Detail;
use App\Panel\Livewire\Submissions\Forms\Publish;
use App\Panel\Livewire\Submissions\Forms\References;
use App\Panel\Livewire\Submissions\PeerReview;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use App\Panel\Resources\SubmissionResource;
use Awcodes\Shout\Components\ShoutEntry;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs as HorizontalTabs;
use Filament\Infolists\Components\Tabs\Tab as HorizontalTab;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Compilers\BladeCompiler;

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

        /**
         * Check if the authenticated user has the 'Editor' role and is assigned to the submission.
         * If the user is not an editor or has additional roles, the request will be aborted with a 403 status code.
         *
         * @return void 
         */
        if (
            auth()->user()->hasRole(UserRole::Editor->value)
            && !auth()->user()->hasRole(UserRole::Admin->value)
            && !auth()->user()->hasRole(UserRole::ConferenceManager->value)
        ) {
            $editorAssgined = $this->record
                ->participants()
                ->where('user_id', auth()->id());

            abort_unless(
                $editorAssgined->exists(),
                403
            );
        }
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function getSubheading(): string|Htmlable|null
    {
        $badgeHtml = match ($this->record->status->value) {
            SubmissionStatus::Queued->value => '<x-filament::badge color="primary" class="w-fit">On Queue</x-filament::badge>',
            SubmissionStatus::Declined->value => '<x-filament::badge color="danger" class="w-fit">Declined</x-filament::badge>',
            SubmissionStatus::Published->value => '<x-filament::badge color="success" class="w-fit">Published</x-filament::badge>',
            SubmissionStatus::OnReview->value => '<x-filament::badge color="warning" class="w-fit">Under Review</x-filament::badge>',
            SubmissionStatus::Incomplete->value => '<x-filament::badge color="secondary" class="w-fit">Incomplete</x-filament::badge>',
            SubmissionStatus::Editing->value => '<x-filament::badge color="info" class="w-fit">Editing</x-filament::badge>',
            default => null,
        };

        return new HtmlString(
            BladeCompiler::render($badgeHtml)
        );
    }

    public function getHeading(): string
    {
        return $this->record->getMeta('title');
    }

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
                                                fn (): bool => StageManager::stage('peer-review')->isStageOpen()
                                            )
                                            ->icon("iconpark-checklist-o")
                                            ->schema([
                                                LivewireEntry::make('peer-review')
                                                    ->livewire(PeerReview::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ]),
                                        Tab::make("Editing")
                                            ->visible(fn (): bool => StageManager::stage('editing')->isStageOpen())
                                            ->icon("heroicon-o-pencil")
                                            ->schema([
                                                LivewireEntry::make('editing')
                                                    ->livewire(Editing::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ])
                                    ])
                                    ->maxWidth('full')
                            ]),
                        HorizontalTab::make('Publication')
                            ->extraAttributes([
                                'x-on:open-publication-tab.window' => new HtmlString('tab = \'-publication-tab\'')
                            ])
                            ->schema([
                                ShoutEntry::make('can-not-edit')
                                    ->color('warning')
                                    ->visible(
                                        fn (): bool => $this->record->isPublished()
                                    )
                                    ->content("You can not edit this submission because it is already published."),
                                Tabs::make()
                                    ->tabs([
                                        Tab::make('Detail')
                                            ->icon("heroicon-o-information-circle")
                                            ->schema([
                                                LivewireEntry::make('detail-form')
                                                    ->livewire(Detail::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ]),
                                        Tab::make('Contributors')
                                            ->icon("heroicon-o-user-group")
                                            ->schema([
                                                LivewireEntry::make('contributors')
                                                    ->livewire(ContributorList::class, [
                                                        'submission' => $this->record,
                                                        'viewOnly' => !auth()->user()->can('Publication:update') || $this->record->isPublished()
                                                    ])
                                            ]),
                                        Tab::make('References')
                                            ->icon("iconpark-list")
                                            ->schema([
                                                LivewireEntry::make('references')
                                                    ->livewire(References::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ]),
                                        Tab::make('Proceeding')
                                            ->icon("iconpark-check-o")
                                            ->hidden(function () {
                                                return $this->record->stage != SubmissionStage::Editing;
                                            })
                                            ->schema([
                                                LivewireEntry::make('publishing')
                                                    ->livewire(Publish::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ]),
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
