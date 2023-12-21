<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;

class PublishedSubmissionState extends BaseSubmissionState
{
    public function unpublish(): void
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::Editing,
            'status' => SubmissionStatus::Editing,
        ], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.unpublished'));
    }
}
