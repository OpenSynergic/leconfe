<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\States\Submission\Concerns\CanWithdraw;

class EditingSubmissionState extends BaseSubmissionState
{
    use CanWithdraw;

    public function publish(): void
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::Proceeding,
            'status' => SubmissionStatus::Published,
            'published_at' => now(),
        ], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.published'));
    }
}
