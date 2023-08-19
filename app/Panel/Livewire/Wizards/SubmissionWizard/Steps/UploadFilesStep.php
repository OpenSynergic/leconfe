<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Models\Submission;
use Livewire\Component;

class UploadFilesStep extends Component implements HasWizardStep
{
    public Submission $record;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public static function getWizardLabel(): string
    {
        return 'Upload Files';
    }

    public function render()
    {
        return view('panel.livewire.wizards.submission-wizard.steps.upload-files-step');
    }

    public function nextStep()
    {
        if ($this->record->getMedia('files')->isEmpty()) {
            return session()->flash('no_files', 'No files were added to the submission');
        }

        SubmissionUpdateAction::run([
            'submission_progress' => 'authors',
        ], $this->record);

        $this->dispatchBrowserEvent('next-wizard-step');
    }
}
