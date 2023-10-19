<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;

class PaperFiles extends SubmissionFilesTable
{
    public string $category = SubmissionFileCategory::PAPER_FILES;

    public function tableHeading(): string
    {
        return "Papers";
    }
}
