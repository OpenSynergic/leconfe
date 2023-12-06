<?php

namespace App\Actions\Submissions;

use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use Lorisleiva\Actions\Concerns\AsAction;

class UnpublishSubmissionAction
{
    use AsAction;

    // When a submission is unpublished, it should be returned to the latest status, which is editing.
    public function handle(Submission $submission)
    {
        SubmissionUpdateAction::run([
            'status' => SubmissionStatus::Editing,
        ], $submission);
    }
}
