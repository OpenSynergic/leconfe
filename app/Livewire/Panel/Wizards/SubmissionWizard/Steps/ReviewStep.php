<?php

namespace App\Livewire\Panel\Wizards\SubmissionWizard\Steps;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Filament\Resources\SubmissionResource;
use App\Livewire\Panel\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use App\Models\Submission;
use Filament\Notifications\Notification;
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
        return view('livewire.panel.wizards.submission-wizard.steps.review-step');
    }

    // TODO Tambahkan nama conference
    public function submit()
    {
        if ($this->record->getMedia('files')->count() == 0 || $this->record->authors->count() == 0) {
            session()->flash('submission_not_complete', 'Your submission cannot be completed as there is one or more issues that need to be addressed. Please carefully review the information provided below and make the necessary changes to proceed with your submission.');

            $this->dispatchBrowserEvent('close-modal', ['id' => 'modalSubmisionWizardConfirmation']);

            return;
        }

        SubmissionUpdateAction::run([
            'submission_progress' => 'review',
            'status' => Submission::STATUS_ACTIVE,
        ], $this->record);

        // TODO kirim email konfirmasi ke pengguna untuk submission yg di input

        return redirect()->to(SubmissionResource::getUrl('complete', $this->record));
    }
}
