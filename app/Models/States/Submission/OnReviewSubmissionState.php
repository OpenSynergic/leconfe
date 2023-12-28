<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Classes\Log;
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

        Log::make(
            name: 'submission',
            subject: $this->submission,
            description: __('log.submission.accepted')
        )
            ->by(auth()->user())
            ->save();
    }

    public function decline(): void
    {
        SubmissionUpdateAction::run([
            'revision_required' => false,
            'stage' => SubmissionStage::PeerReview,
            'status' => SubmissionStatus::Declined,
        ], $this->submission);

        Log::make(
            name: 'submission',
            subject: $this->submission,
            description: __('log.submission.declined')
        )
            ->by(auth()->user())
            ->save();
    }

    public function skipReview(): void
    {
        SubmissionUpdateAction::run([
            'skipped_review' => true,
            'revision_required' => false,
            'status' => SubmissionStatus::Editing,
            'stage' => SubmissionStage::Editing,
        ], $this->submission);

        Log::make(
            name: 'submission',
            subject: $this->submission,
            description: __('log.submission.skip_review')
        )
            ->by(auth()->user())
            ->save();
    }

    public function requestRevision(): void
    {
        SubmissionUpdateAction::run([
            'revision_required' => true,
        ], $this->submission);

        Log::make(
            name: 'submission',
            subject: $this->submission,
            description: __('log.submission.revision_required')
        )
            ->by(auth()->user())
            ->save();
    }
}