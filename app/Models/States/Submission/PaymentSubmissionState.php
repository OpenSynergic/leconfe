<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Managers\PaymentManager;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;

class PaymentSubmissionState extends BaseSubmissionState
{
    public function pay(): void
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::PeerReview,
            'status' => SubmissionStatus::OnReview,
        ], $this->submission);

    }

    public function withdraw(): void
    {
        SubmissionUpdateAction::run(['status' => SubmissionStatus::Withdrawn], $this->submission);
    }
}
