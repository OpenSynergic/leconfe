<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Classes\Log;
use App\Events\Submissions\Published;
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

        Published::dispatch($this->submission);

        Log::make(
            name: 'submission',
            subject: $this->submission,
            description: __('log.submission.published')
        )
            ->by(auth()->user())
            ->save();
    }
}
