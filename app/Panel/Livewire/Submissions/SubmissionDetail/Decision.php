<?php

namespace App\Panel\Livewire\Submissions\SubmissionDetail;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Submissions\SubmissionDetail;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class Decision extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Submission $submission;

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function acceptAction()
    {
        return Action::make('acceptAction')
            ->record($this->submission)
            ->icon("lineawesome-check-circle-solid")
            ->label(function (Submission $record) {
                if ($record->status === SubmissionStatus::New) {
                    return "Accept Submission";
                }
                return "Submission Accepted";
            })
            ->disabled(
                fn (): bool => $this->submission->status != SubmissionStatus::New
            )
            ->outlined()
            ->color("primary")
            ->successNotificationTitle("Submission Accepted")
            ->action(function (Action $action, Submission $record) {
                SubmissionUpdateAction::run([
                    'status' => SubmissionStatus::Accepted
                ], $this->submission);
                $action->success();
            })
            ->requiresConfirmation();
    }

    public function declineAction()
    {
        return Action::make('declineAction')
            ->icon("lineawesome-times-circle-solid")
            ->label("Decline Submission")
            ->outlined()
            ->requiresConfirmation()
            ->disabled(
                fn (): bool => $this->submission->status != SubmissionStatus::New
            )
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'status' => SubmissionStatus::Declined
                ], $this->submission);
                $action->success();
            })
            ->color("danger");
    }

    public function render()
    {
        return view('panel.livewire.submissions.submission-detail.decision');
    }
}
