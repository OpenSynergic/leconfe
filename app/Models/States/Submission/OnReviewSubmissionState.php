<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\States\Submission\Concerns\CanWithdraw;

class OnReviewSubmissionState extends BaseSubmissionState
{
    use CanWithdraw;

    public function accept(): void
    {
        SubmissionUpdateAction::run([
            'revision_required' => false,
            'stage' => SubmissionStage::Editing,
            'status' => SubmissionStatus::Editing,
        ], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.accepted'));
    }

    public function decline(): void
    {
        SubmissionUpdateAction::run([
            'revision_required' => false,
            'stage' => SubmissionStage::PeerReview,
            'status' => SubmissionStatus::Declined,
        ], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.declined'));
    }

    public function skipReview(): void
    {
        SubmissionUpdateAction::run([
            'skipped_review' => true,
            'revision_required' => false,
            'status' => SubmissionStatus::Editing,
            'stage' => SubmissionStage::Editing,
        ], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.skip_review'));
    }

    public function requestRevision(): void
    {
        SubmissionUpdateAction::run([
            'revision_required' => true,
        ], $this->submission);

        activity('submission')
            ->performedOn($this->submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.revision_required'));
    }
}
