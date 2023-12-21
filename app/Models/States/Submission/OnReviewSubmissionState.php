<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;

class OnReviewSubmissionState extends BaseSubmissionState
{
    public function accept(): void
    {
        SubmissionUpdateAction::run([
            'revision_required' => false,
            'stage' => SubmissionStage::Editing,
            'status' => SubmissionStatus::Editing,
        ], $this->submission);
    }

    public function withdraw(): void
    {
        SubmissionUpdateAction::run(['status' => SubmissionStatus::Withdrawn], $this->submission);
    }
}
