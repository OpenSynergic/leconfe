<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use App\Models\Enums\SubmissionStage;

class PaperFiles extends SubmissionFilesTable
{
    public string $category = SubmissionFileCategory::PAPER_FILES;

    public function isViewOnly(): bool
    {
        if ($this->viewOnly) {
            return $this->viewOnly;
        }

        return $this->submission->stage != SubmissionStage::PeerReview;
    }

    public function tableHeading(): string
    {
        return "Papers";
    }
}
