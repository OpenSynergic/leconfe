<?php

namespace App\Panel\Conference\Livewire\Wizards\SubmissionWizard\Steps;

use App\Mail\Templates\ThankAuthorMail;
use App\Models\Enums\UserRole;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\NewSubmission;
use App\Panel\Conference\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Panel\Conference\Resources\SubmissionResource;
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
                return 'You will be submitting your abstract to the conference, Please review your submission carefully before proceeding.';
            })
            ->modalSubmitActionLabel('Submit')
            ->successNotificationTitle('Abstract submitted, please wait for the conference manager to review your submission.')
            ->successRedirectUrl(fn (): string => SubmissionResource::getUrl('complete', ['record' => $this->record]))
            ->action(function (Action $action) {
                try {
                    $this->record->state()->fulfill();

                    Mail::to($this->record->user)->send(
                        new ThankAuthorMail($this->record)
                    );

                    User::role([UserRole::Admin->value, UserRole::ConferenceManager->value])
                        ->lazy()
                        ->each(fn ($user) => $user->notify(new NewSubmission($this->record)));
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
        return view('panel.conference.livewire.wizards.submission-wizard.steps.review-step');
    }
}
