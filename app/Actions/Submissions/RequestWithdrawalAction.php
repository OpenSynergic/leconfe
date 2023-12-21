<?php

namespace App\Actions\Submissions;

use App\Models\Submission;
use Lorisleiva\Actions\Concerns\AsAction;

class RequestWithdrawalAction
{
    use AsAction;

    public function handle(Submission $submission, ?string $reason = null)
    {
        SubmissionUpdateAction::run(['withdrawn_reason' => $reason], $submission);

        activity('submission')
            ->performedOn($submission)
            ->causedBy(auth()->user())
            ->log(__('log.submission.requested_withdrawal'));
    }
}
