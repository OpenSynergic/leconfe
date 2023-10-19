<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;

class DraftFiles extends SubmissionFilesTable
{
    public string $category = SubmissionFileCategory::EDITING_DRAFT_FILES;

    public function tableHeading(): string
    {
        return "Draft Files";
    }
}
