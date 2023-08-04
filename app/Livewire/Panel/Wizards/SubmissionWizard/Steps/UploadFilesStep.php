<?php

namespace App\Livewire\Panel\Wizards\SubmissionWizard\Steps;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Livewire\Panel\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Models\Submission;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

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
        return view('livewire.panel.wizards.submission-wizard.steps.upload-files-step');
    }

    public function nextStep()
    {
        if ($this->record->getMedia('files')->isEmpty()) {
            return session()->flash('no_files', 'No files were added to the submission');
        }

        SubmissionUpdateAction::run([
            'submission_progress' => 'authors'
        ], $this->record);


        $this->dispatchBrowserEvent('next-wizard-step');
    }
}
