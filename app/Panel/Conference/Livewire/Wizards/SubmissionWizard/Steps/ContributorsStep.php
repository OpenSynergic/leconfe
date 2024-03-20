<?php

namespace App\Panel\Conference\Livewire\Wizards\SubmissionWizard\Steps;

use App\Models\Submission;
use App\Panel\Conference\Livewire\Wizards\SubmissionWizard\Contracts\HasWizardStep;
use Livewire\Component;

class ContributorsStep extends Component implements HasWizardStep
{
    public Submission $record;

    public static function getWizardLabel(): string
    {
        return 'Contributors';
    }

    public function render()
    {
        return view('panel.conference.livewire.wizards.submission-wizard.steps.authors-step');
    }

    public function nextStep()
    {
        if (! $this->record->participants()->exists()) {
            $this->addError('errors', 'You must add at least one author');

            return;
        }

        $this->dispatch('refreshLivewire');
        $this->dispatch('refreshAbstractsFiles');
        $this->dispatch('next-wizard-step');
    }
}
