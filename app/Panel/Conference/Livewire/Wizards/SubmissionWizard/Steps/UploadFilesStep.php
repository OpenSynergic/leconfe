<?php

namespace App\Panel\Conference\Livewire\Wizards\SubmissionWizard\Steps;

use App\Models\Submission;
use App\Panel\Conference\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use Filament\Actions\Action as PageAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class UploadFilesStep extends Component implements HasActions, HasForms, HasWizardStep
{
    use InteractsWithActions, InteractsWithForms;

    public Submission $record;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public static function getWizardLabel(): string
    {
        return 'Upload Abstract';
    }

    public function render()
    {
        return view('panel.conference.livewire.wizards.submission-wizard.steps.upload-files-step');
    }

    public function nextStep()
    {
        return PageAction::make('nextStep')
            ->label('Next')
            ->failureNotificationTitle('No files were added to the submission')
            ->successNotificationTitle('Saved')
            ->action(function (PageAction $action) {
                if (! $this->record->submissionFiles()->exists()) {
                    return $action->failure();
                }
                $action->success();
                $this->dispatch('next-wizard-step');
            });
    }
}
