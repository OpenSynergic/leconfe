<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Classes\Log;
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

        Log::make(
            name: 'submission',
            subject: $this->submission,
            description: __('log.submission.abstract_accepted')
        )
            ->by(auth()->user())
            ->save();
    }

    public function decline(): void
    {
        SubmissionUpdateAction::run([
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
}
