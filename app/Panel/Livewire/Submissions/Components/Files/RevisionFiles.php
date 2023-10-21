<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use App\Models\Enums\SubmissionStage;

class RevisionFiles extends SubmissionFilesTable
{
    protected ?string $category = SubmissionFileCategory::REVISION_FILES;

    protected string $tableHeading = "Revisions";

    public function isViewOnly(): bool
    {
        return $this->submission->stage != SubmissionStage::PeerReview || !$this->submission->revision_required;
    }
}
