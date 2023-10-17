<?php

namespace App\Panel\Livewire\Submissions;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

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
        $this->stageOpened = app()->getCurrentConference()->getMeta("workflow.peer-review.open", false);
    }

    public function declineSubmissionAction()
    {
        return Action::make('declineSubmissionAction')
            ->label("Decline Submission")
            ->color('danger')
            ->outlined()
            ->form([])
            ->action(function (Action $action) {
                $action->success();
            });
    }


    public function acceptSubmissionAction()
    {
        return Action::make('acceptSubmissionAction')
            // ->outlined()
            ->color("primary")
            ->label("Accept Submission")
            ->form([])
            ->action(function (Action $action) {
                $action->success();
            });
    }

    public function requestRevisionAction()
    {
        return Action::make('requestRevisionAction')
            ->outlined()
            ->color('gray')
            ->label("Request Revision")
            ->form([])
            ->successNotificationTitle("Revision Requested")
            ->action(function (Action $action) {
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
