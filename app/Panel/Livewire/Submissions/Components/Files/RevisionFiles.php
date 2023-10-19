<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use App\Models\Enums\SubmissionStage;

class RevisionFiles extends SubmissionFilesTable
{
    public string $category = SubmissionFileCategory::REVISION_FILES;

    public function isViewOnly(): bool
    {
        return $this->submission->stage != SubmissionStage::PeerReview || !$this->submission->revision_required;
    }

    public function tableHeading(): string
    {
        return "Revisions";
    }
}
