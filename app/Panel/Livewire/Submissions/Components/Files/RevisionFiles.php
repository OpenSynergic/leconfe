<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;

class RevisionFiles extends SubmissionFilesTable
{
    public string $category = SubmissionFileCategory::REVISION_FILES;

    public function tableHeading(): string
    {
        return "Revisions";
    }
}
