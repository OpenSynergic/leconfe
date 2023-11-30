<?php

namespace App\Actions\Submissions;

use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use Lorisleiva\Actions\Concerns\AsAction;

class AcceptWithdrawalAction
{
    use AsAction;

    public function handle(Submission $submission)
    {
        SubmissionUpdateAction::run(['status' => SubmissionStatus::Withdrawn], $submission);
    }
}
