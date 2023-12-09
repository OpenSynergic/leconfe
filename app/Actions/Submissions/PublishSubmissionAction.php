<?php

namespace App\Actions\Submissions;

use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use Lorisleiva\Actions\Concerns\AsAction;

class PublishSubmissionAction
{
    use AsAction;

    public function handle(Submission $submission)
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::Proceeding,
            'status' => SubmissionStatus::Published,
            'published_at' => now()
        ], $submission);
    }
}
