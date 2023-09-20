<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Steps;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Panel\Resources\SubmissionResource;
use Livewire\Component;

class ReviewStep extends Component implements HasWizardStep
{
    public Submission $record;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public static function getWizardLabel(): string
    {
        return 'Review';
    }

    public function render()
    {
        return view('panel.livewire.wizards.submission-wizard.steps.review-step');
    }

    // TODO Tambahkan nama conference
    public function submit()
    {
        if (!$this->record->files()->exists() || !$this->record->participants()->exists()) {
            $this->addError("errors", "Your submission cannot be completed as there is one or more issues that need to be addressed. Please carefully review the information provided below and make the necessary changes to proceed with your submission.");
            $this->dispatchBrowserEvent('close-modal', ['id' => 'modalSubmisionWizardConfirmation']);
            return;
        }

        SubmissionUpdateAction::run([
            'submission_progress' => 'review',
            'status' => SubmissionStatus::New,
        ], $this->record);

        /**
         * 
         * TODO:
         * - Send Mail
         *  to:
         *      - main author
         *          - Terima kasih telah mengirimkan paper. Anda dapat memantau perkembangannya melalui tautan di bawah ini.
         *      - conference manager
         *          - 1 Paper telah diterima dengan judul [title], silakan assign salah satu director conference, 
         *          - (Improvement) ada auto login di email notifikasi
         */
        return redirect()->to(SubmissionResource::getUrl('complete', ['record' => $this->record]));
    }
}
