<?php

namespace App\Models\States\Submission\Concerns;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStatus;

trait CanWithdraw
{
    public function withdraw(): void
    {
        SubmissionUpdateAction::run(['status' => SubmissionStatus::Withdrawn], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.withdrawn'));
    }
}
