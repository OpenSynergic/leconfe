<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;

class EditingSubmissionState extends BaseSubmissionState
{
    public function publish(): void
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::Proceeding,
            'status' => SubmissionStatus::Published,
        ], $this->submission);
    }

    public function withdraw(): void
    {
        SubmissionUpdateAction::run(['status' => SubmissionStatus::Withdrawn], $this->submission);
    }
}
