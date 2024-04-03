<?php

namespace App\Repositories\Submission;

use App\Models\Enums\SubmissionStatus;
use App\Models\States\Submission\BaseSubmissionState;
use App\Models\States\Submission\DeclinedSubmissionState;
use App\Models\States\Submission\EditingSubmissionState;
use App\Models\States\Submission\IncompleteSubmissionState;
use App\Models\States\Submission\OnReviewSubmissionState;
use App\Models\States\Submission\PublishedSubmissionState;
use App\Models\States\Submission\QueuedSubmissionState;
use App\Models\States\Submission\WithdrawnSubmissionState;
use App\Models\Submission;
use App\Repositories\BaseRepository;

class SubmissionRepository extends BaseRepository
{

    public function getModel(): Submission
    {
        return new Submission();
    }

    public function getState(Submission $submission): BaseSubmissionState
    {
        return match ($submission->status) {
            SubmissionStatus::Incomplete => new IncompleteSubmissionState($submission),
            SubmissionStatus::Queued => new QueuedSubmissionState($submission),
            SubmissionStatus::OnReview => new OnReviewSubmissionState($submission),
            SubmissionStatus::Editing => new EditingSubmissionState($submission),
            SubmissionStatus::Published => new PublishedSubmissionState($submission),
            SubmissionStatus::Declined => new DeclinedSubmissionState($submission),
            SubmissionStatus::Withdrawn => new WithdrawnSubmissionState($submission),
            default => throw new \Exception('Invalid submission status'),
        };
    }
}
