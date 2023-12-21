<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\States\Submission\Concerns\CanWithdraw;

class QueuedSubmissionState extends BaseSubmissionState
{
    use CanWithdraw;

    public function acceptAbstract(): void
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::PeerReview,
            'status' => SubmissionStatus::OnReview,
        ], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log('log.submission.abstract_accepted');
    }

    public function decline(): void
    {
        SubmissionUpdateAction::run([
            'status' => SubmissionStatus::Declined,
        ], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.declined'));
    }
}
