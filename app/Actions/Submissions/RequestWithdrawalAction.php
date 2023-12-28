<?php

namespace App\Actions\Submissions;

use App\Classes\Log;
use App\Models\Submission;
use Lorisleiva\Actions\Concerns\AsAction;

class RequestWithdrawalAction
{
    use AsAction;

    public function handle(Submission $submission, ?string $reason = null)
    {
        SubmissionUpdateAction::run(['withdrawn_reason' => $reason], $submission);

        Log::make(
            name: 'submission',
            subject: $submission,
            description: __('log.submission.requested_withdrawal'),
        )
            ->by(auth()->user())
            ->save();
    }
}
