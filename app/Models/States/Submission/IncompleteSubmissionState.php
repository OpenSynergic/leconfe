<?php

namespace App\Models\States\Submission;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Classes\Log;
use App\Mail\Templates\ThankAuthorMail;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Notifications\NewSubmission;
use Illuminate\Support\Facades\Mail;

class IncompleteSubmissionState extends BaseSubmissionState
{
    public function fulfill(): void
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::CallforAbstract,
            'status' => SubmissionStatus::Queued,
        ], $this->submission);

        Log::make(
            name: 'submission',
            subject: $this->submission,
            description: __('log.submission.created')
        )
            ->by(auth()->user())
            ->save();

        Mail::to($this->submission->user)->send(
            new ThankAuthorMail($this->submission)
        );

        User::role([UserRole::ConferenceManager->value])
            ->lazy()
            ->each(fn ($user) => $user->notify(new NewSubmission($this->submission)));
    }
}
