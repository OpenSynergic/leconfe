<?php

namespace App\Panel\Conference\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use App\Panel\Conference\Livewire\Workflows\Classes\StageManager;

class DraftFiles extends SubmissionFilesTable
{
    use Traits\CanSelectFiles;

    protected ?string $category = SubmissionFileCategory::EDITING_DRAFT_FILES;

    protected string $tableHeading = 'Draft Files';

    public function isViewOnly(): bool
    {
        if ($this->viewOnly) {
            return $this->viewOnly;
        }

        return ! auth()->user()->can('editing', $this->submission);
    }

    public function getTargetCategory(): string
    {
        return $this->getCategory();
    }

    public function getAcceptedFiles(): array
    {
        return StageManager::editing()
            ->getSetting(
                'draft_allowed_file_types',
                ['pdf', 'doc', 'docx']
            );
    }

    public function getSelectableCategories(): array
    {
        return [
            SubmissionFileCategory::PAPER_FILES,
            SubmissionFileCategory::REVIEWER_FILES,
            SubmissionFileCategory::REVISION_FILES,
        ];
    }
}
