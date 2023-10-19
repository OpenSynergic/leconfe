<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;

class ReviewerFiles extends SubmissionFilesTable
{
    public string $category = SubmissionFileCategory::REVIEWER_FILES;

    public function tableHeading(): string
    {
        return "Reviewer Files";
    }
}
