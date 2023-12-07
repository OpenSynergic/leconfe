<?php

namespace App\Actions\Submissions;

use App\Models\Submission;
use Lorisleiva\Actions\Concerns\AsAction;

// This actions is used to reject withdrawal request.
class CancelWithdrawalAction
{
    use AsAction;

    public function handle(Submission $submission)
    {
        SubmissionUpdateAction::run(['withdrawn_reason' => null], $submission);
    }
}
