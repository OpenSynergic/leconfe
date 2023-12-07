<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Mail\Templates\ThankAuthorMail;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\NewSubmission;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Panel\Resources\SubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ReviewStep extends Component implements HasActions, HasForms, HasWizardStep
{
    use InteractsWithActions, InteractsWithForms;

    public Submission $record;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public static function getWizardLabel(): string
    {
        return 'Review';
    }

    public function submitAction()
    {
        return Action::make('submitAction')
            ->label('Submit')
            ->modalWidth('xl')
            ->modalAlignment('center')
            ->requiresConfirmation()
            ->modalHeading('Submit abstract')
            ->modalDescription(function (): string {
                return 'Your about to submit your abstract to the conference, Please review your submission carefully before proceeding.';
            })
            ->modalSubmitActionLabel('Submit')
            ->successNotificationTitle('Abstract submitted, please wait for the conference manager to review your submission.')
            ->successRedirectUrl(fn (): string => SubmissionResource::getUrl('complete', ['record' => $this->record]))
            ->action(function (Action $action) {

                SubmissionUpdateAction::run([
                    'stage' => SubmissionStage::CallforAbstract,
                    'status' => SubmissionStatus::Queued,
                ], $this->record);

                try {
                    Mail::to($this->record->user)->send(
                        new ThankAuthorMail($this->record)
                    );

                    $users = User::role([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value,
                    ])->get();

                    $users->each(fn ($user) => $user->notify(new NewSubmission($this->record)));
                } catch (\Exception $e) {
                    $action->failureNotificationTitle('Failed to send notifications');
                    $action->failure();
                }

                $action->success();
                $action->dispatchSuccessRedirect();
            });
    }

    public function render()
    {
        return view('panel.livewire.wizards.submission-wizard.steps.review-step');
    }
}
