<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Actions\Submissions\AcceptWithdrawalAction;
use App\Actions\Submissions\RequestWithdrawalAction;
use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs\Tab as Tab;
use App\Infolists\Components\VerticalTabs\Tabs as Tabs;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Notifications\SubmissionWithdrawn;
use App\Notifications\SubmissionWithdrawRequested;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Tabs as HorizontalTabs;
use Filament\Infolists\Components\Tabs\Tab as HorizontalTab;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
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

        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('request_withdraw')
                ->color('danger')
                ->authorize('requestWithdraw', $this->record)
                ->label("Request for Withdrawal")
                ->icon('lineawesome-times-circle-solid')
                ->form([
                    Textarea::make('reason')
                        ->hint('Optional')
                        ->placeholder('Reason for withdrawal')
                        ->label("Reason")
                ])
                ->requiresConfirmation()
                ->successNotificationTitle("Withdraw Requested, Please wait for editor to approve")
                ->action(function (Action $action, array $data) {
                    RequestWithdrawalAction::run(
                        $this->record,
                        $data['reason']
                    );

                    try {
                        // Currently using admin, next is admin removed only managers
                        User::whereHas(
                            'roles',
                            fn ($query) => $query->whereIn('name', [UserRole::Admin->value, UserRole::ConferenceManager->value])
                        )
                            ->get()
                            ->each(
                                fn ($manager) => $manager->notify(new SubmissionWithdrawRequested($this->record))
                            );

                        $this->record->getEditors()
                            ->each(
                                fn ($editor) => $editor->notify(new SubmissionWithdrawRequested($this->record))
                            );
                    } catch (\Exception $e) {
                        $action->failureNotificationTitle("Failed to send notification");
                        $action->failure();
                    }

                    $action->successRedirectUrl(
                        SubmissionResource::getUrl('view', [
                            'record' => $this->record,
                            'stage' => '-' . str($this->record->stage->value)->slug('-') . '-tab'
                        ]),
                    );
                    $action->success();
                })
                ->modalWidth('xl'),
            Action::make('withdraw')
                ->modalWidth('xl')
                ->color('danger')
                ->authorize('withdraw', $this->record)
                ->mountUsing(function (Form $form) {
                    $form->fill([
                        'reason' => $this->record->withdrawn_reason
                    ]);
                })
                ->form([
                    Textarea::make('reason')
                        ->readonly()
                        ->hint('Read Only')
                        ->placeholder('Reason for withdrawal')
                        ->label("Reason")
                ])
                ->requiresConfirmation()
                ->modalHeading("Are you sure you want to withdraw this submission?")
                ->modalDescription("You will not be able to undo this action.")
                ->modalSubmitActionLabel("Withdraw")
                ->successNotificationTitle("Withdrawn")
                ->action(function (Action $action) {
                    AcceptWithdrawalAction::run($this->record);
                    try {
                        $this->record->user->notify(
                            new SubmissionWithdrawn($this->record)
                        );
                    } catch (\Exception $e) {
                        $action->failureNotificationTitle("Failed to send notification");
                        $action->failure();
                    }
                    $action->successRedirectUrl(
                        SubmissionResource::getUrl('view', [
                            'record' => $this->record,
                            'stage' => '-' . str($this->record->stage->value)->slug('-') . '-tab'
                        ]),
                    );
                    $action->success();
                })
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        $badgeHtml = match ($this->record->status) {
            SubmissionStatus::Queued => '<x-filament::badge color="primary" class="w-fit">' . SubmissionStatus::Queued->value . '</x-filament::badge>',
            SubmissionStatus::Declined => '<x-filament::badge color="danger" class="w-fit">' . SubmissionStatus::Declined->value . '</x-filament::badge>',
            SubmissionStatus::Withdrawn => '<x-filament::badge color="danger" class="w-fit">' . SubmissionStatus::Withdrawn->value . '</x-filament::badge>',
            SubmissionStatus::Published => '<x-filament::badge color="success" class="w-fit">' . SubmissionStatus::Published->value . '</x-filament::badge>',
            SubmissionStatus::OnReview => '<x-filament::badge color="warning" class="w-fit">' . SubmissionStatus::OnReview->value . '</x-filament::badge>',
            SubmissionStatus::Incomplete => '<x-filament::badge color="gray" class="w-fit">' . SubmissionStatus::Incomplete->value . '</x-filament::badge>',
            SubmissionStatus::Editing => '<x-filament::badge color="info" class="w-fit">' . SubmissionStatus::Editing->value . '</x-filament::badge>',
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
        return $infolist
            ->schema([
                ShoutEntry::make('withdraw-information')
                    ->visible(
                        fn (): bool => filled($this->record->withdrawn_reason) && $this->record->status != SubmissionStatus::Withdrawn
                    )
                    ->content(function (): string {
                        return "The submission is currently awaiting approval for the withdrawal request.";
                    })
                    ->color('warning')
                    ->type('info'),
                HorizontalTabs::make()
                    ->persistTabInQueryString('tab')
                    ->contained(false)
                    ->tabs([
                        HorizontalTab::make('Workflow')
                            ->schema([
                                Tabs::make()
                                    ->persistTabInQueryString('stage')
                                    ->sticky()
                                    ->tabs([
                                        Tab::make("Call for Abstract")
                                            ->icon("heroicon-o-information-circle")
                                            ->schema([
                                                LivewireEntry::make('call-for-abstract')
                                                    ->livewire(CallforAbstract::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ]),
                                        Tab::make("Peer Review")
                                            ->visible(
                                                fn (): bool => StageManager::peerReview()->isStageOpen()
                                            )
                                            ->icon("iconpark-checklist-o")
                                            ->schema([
                                                LivewireEntry::make('peer-review')
                                                    ->livewire(PeerReview::class, [
                                                        'submission' => $this->record
                                                    ])
                                            ]),
                                        Tab::make("Editing")
                                            ->visible(fn (): bool => StageManager::editing()->isStageOpen())
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
                                    ->type('warning')
                                    ->color('warning')
                                    ->visible(
                                        fn (): bool => $this->record->isPublished()
                                    )
                                    ->content("You can't edit this submission because it is already published."),
                                Tabs::make()
                                    ->persistTabInQueryString('ptab') // ptab shorten of publication-tab
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
                                                        'viewOnly' => !auth()->user()->can('editing', $this->record),
                                                    ])
                                            ]),
                                        Tab::make('References')
                                            ->icon("iconpark-list")
                                            ->schema([
                                                LivewireEntry::make('references')
                                                    ->livewire(References::class, [
                                                        'submission' => $this->record,
                                                    ])
                                            ]),
                                        Tab::make('Proceeding')
                                            ->icon("iconpark-check-o")
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
