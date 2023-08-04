<?php

namespace App\Livewire\Panel\Wizards;

use App\Livewire\Panel\Wizards\SubmissionWizard\Steps\AuthorsStep;
use App\Livewire\Panel\Wizards\SubmissionWizard\Steps\DetailStep;
use App\Livewire\Panel\Wizards\SubmissionWizard\Steps\ForTheEditorsStep;
use App\Livewire\Panel\Wizards\SubmissionWizard\Steps\ReviewStep;
use App\Livewire\Panel\Wizards\SubmissionWizard\Steps\UploadFilesStep;
use App\Models\Submission;
use Livewire\Component;

class SubmissionWizard extends Component
{
    public Submission $record;

    public function render()
    {
        return view('livewire.panel.wizards.submission-wizard');
    }

    public function steps(): array
    {
        return [
            'detail' => DetailStep::class,
            'upload-files' => UploadFilesStep::class,
            'authors' => AuthorsStep::class,
            // 'for-the-editor' => ForTheEditorsStep::class,
            'review' => ReviewStep::class,
        ];
    }

    public function getStartStep(): int
    {
        $queryStringStep = request()->query('step');

        foreach ($this->getStepKeys() as $index => $step) {
            if ($step !== $queryStringStep) {
                continue;
            }

            return $index + 1;
        }

        return 1;
    }

    public function getStepKeys()
    {
        return array_keys($this->steps());
    }
}
