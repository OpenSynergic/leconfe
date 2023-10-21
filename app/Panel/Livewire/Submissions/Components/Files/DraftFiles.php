<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;

class DraftFiles extends SubmissionFilesTable
{
    use Traits\CanSelectFiles;

    protected ?string $category = SubmissionFileCategory::EDITING_DRAFT_FILES;

    protected string $tableHeading = "Draft Files";

    public function getTargetCategory(): string
    {
        return $this->getCategory();
    }

    public function getSelectableCategories(): array
    {
        return [
            SubmissionFileCategory::PAPER_FILES,
            SubmissionFileCategory::REVIEWER_FILES,
            SubmissionFileCategory::REVISION_FILES
        ];
    }
}
