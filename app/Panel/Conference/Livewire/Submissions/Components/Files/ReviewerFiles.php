<?php

namespace App\Panel\Conference\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;

class ReviewerFiles extends SubmissionFilesTable
{
    protected ?string $category = SubmissionFileCategory::REVIEWER_FILES;

    protected string $tableHeading = 'Reviewer Files';
}
