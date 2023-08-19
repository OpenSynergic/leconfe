<?php

namespace App\Panel\Livewire\Wizards;

use App\Panel\Livewire\Wizards\SubmissionWizard\Steps\AuthorsStep;
use App\Panel\Livewire\Wizards\SubmissionWizard\Steps\DetailStep;
use App\Panel\Livewire\Wizards\SubmissionWizard\Steps\ForTheEditorsStep;
use App\Panel\Livewire\Wizards\SubmissionWizard\Steps\ReviewStep;
use App\Panel\Livewire\Wizards\SubmissionWizard\Steps\UploadFilesStep;
use App\Models\Submission;
use Livewire\Component;

class SubmissionWizard extends Component
{
    public Submission $record;

    public function render()
    {
        return view('panel.livewire.wizards.submission-wizard');
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
