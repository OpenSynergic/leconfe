<?php

namespace App\Panel\Conference\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use Awcodes\Shout\Components\Shout;

class PaperFiles extends SubmissionFilesTable
{
    protected ?string $category = SubmissionFileCategory::PAPER_FILES;

    protected string $tableHeading = 'Papers';

    public function isViewOnly(): bool
    {
        return ! auth()->user()->can('uploadPaper', $this->submission);
    }

    public function uploadFormSchema(): array
    {
        return [
            Shout::make('information')
                ->content('After uploading your paper, system will send notification to the editor.'),
            ...parent::uploadFormSchema(),
        ];
    }
}
