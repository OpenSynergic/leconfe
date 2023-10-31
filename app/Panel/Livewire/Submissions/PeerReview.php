<?php

namespace App\Panel\Livewire\Submissions;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PeerReview extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    public Submission $submission;

    public bool $stageOpened = false;

    protected $listeners = [
        'refreshPeerReview' => '$refresh'
    ];

    public function mount(Submission $submission)
    {
        $this->stageOpened = StageManager::stage('peer-review')->isStageOpen();
    }

    public function declineSubmissionAction()
    {
        return Action::make('declineSubmissionAction')
            ->icon("lineawesome-times-solid")
            ->label("Decline Submission")
            ->color('danger')
            ->outlined()
            ->form([
                TinyEditor::make('message')
                    ->minHeight(300)
                    ->hidden(fn (Get $get) => $get('do-not-notify-author'))
                    ->columnSpanFull(),
                Checkbox::make('do-not-notify-author')
                    ->reactive()
                    ->label("Don't Send Notification to Author")
                    ->columnSpanFull(),
            ])
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'revision_required' => false,
                    'status' => SubmissionStatus::Declined,
                ], $this->submission);
                $action->success();
            });
    }


    public function acceptSubmissionAction()
    {
        return Action::make('acceptSubmissionAction')
            ->icon("fluentui-checkmark-16-o")
            ->color("primary")
            ->label("Accept Submission")
            ->modalSubmitActionLabel("Accept")
            ->form([
                TinyEditor::make('message')
                    ->minHeight(300)
                    ->hidden(fn (Get $get) => $get('do-not-notify-author'))
                    ->columnSpanFull(),
                Checkbox::make('do-not-notify-author')
                    ->reactive()
                    ->label("Don't Send Notification to Author")
                    ->columnSpanFull(),
            ])
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'revision_required' => false,
                    'stage' => SubmissionStage::Editing,
                    'status' => SubmissionStatus::Editing,
                ], $this->submission);
                $action->success();
            });
    }

    public function requestRevisionAction()
    {
        return Action::make('requestRevisionAction')
            ->hidden(fn (): bool => $this->submission->revision_required)
            ->icon("lineawesome-list-alt-solid")
            ->outlined()
            ->color('gray')
            ->label("Request Revision")
            ->form([
                TinyEditor::make('message')
                    ->minHeight(300)
                    ->hidden(fn (Get $get) => $get('do-not-notify-author'))
                    ->columnSpanFull(),
                Checkbox::make('do-not-notify-author')
                    ->reactive()
                    ->label("Don't Send Notification to Author")
                    ->columnSpanFull(),
            ])
            ->successNotificationTitle("Revision Requested")
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'revision_required' => true
                ], $this->submission);
                $action->success();
            });
    }

    public function skipReviewAction()
    {
        return Action::make('skipReviewAction')
            ->label('Skip Review')
            ->icon("lineawesome-check-circle-solid")
            ->color('gray')
            ->outlined()
            ->successNotificationTitle("Review Skipped")
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'skipped_review' => true,
                ], $this->submission);
                $action->success();
            })
            ->requiresConfirmation();
    }

    public function render()
    {
        return view('panel.livewire.submissions.peer-review');
    }
}
