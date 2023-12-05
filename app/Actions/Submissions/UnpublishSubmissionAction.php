<?php

namespace App\Actions\Submissions;

use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use Lorisleiva\Actions\Concerns\AsAction;

class UnpublishSubmissionAction
{
    use AsAction;

    public function handle(Submission $submission)
    {
        SubmissionUpdateAction::run(['status' => SubmissionStatus::Unpublished], $submission);
    }
}
