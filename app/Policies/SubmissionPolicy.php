<?php

namespace App\Policies;

use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use App\Panel\Livewire\Workflows\Classes\StageManager;

class SubmissionPolicy
{
    public function create(User $user)
    {
        if ($user->can('Submission:create')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('Submission:viewAny');
    }

    public function view(User $user)
    {
        if ($user->can('Submission:view')) {
            return true;
        }
    }

    public function update(User $user, Submission $submission)
    {
        if ($submission->status == SubmissionStatus::Published) {
            return false;
        }

        if ($user->can('Submission:update')) {
            return true;
        }
    }

    public function delete(User $user, Submission $submission)
    {
        if ($submission->status != SubmissionStatus::Declined) {
            return false;
        }

        if ($user->can('Submission:delete')) {
            return true;
        }
    }

    public function assignReviewer(User $user, Submission $submission)
    {
        if ($submission->stage != SubmissionStage::PeerReview) {
            return false;
        }

        if ($submission->status == SubmissionStatus::Declined) {
            return false;
        }

        if ($user->can('Submission:assignReviewer')) {
            return true;
        }
    }

    public function declineReview(User $user, Submission $submission)
    {
        if ($submission->status == SubmissionStatus::Declined) {
            return false;
        }

        if ($submission->stage != SubmissionStage::PeerReview) {
            return false;
        }

        if ($user->can('Submission:declinePaper')) {
            return true;
        }
    }

    public function acceptReview(User $user, Submission $submission)
    {
        if ($submission->status == SubmissionStatus::Declined) {
            return false;
        }

        if ($submission->stage != SubmissionStage::PeerReview) {
            return false;
        }

        if ($user->can('Submission:acceptPaper')) {
            return true;
        }
    }

    public function declineAbstract(User $user, Submission $submission)
    {
        if ($submission->status == SubmissionStatus::Declined) {
            return false;
        }

        if ($submission->stage != SubmissionStage::CallforAbstract) {
            return false;
        }

        if ($user->can('Submission:declineAbstract')) {
            return true;
        }
    }

    public function acceptAbstract(User $user, Submission $submission)
    {
        if ($submission->status == SubmissionStatus::Declined) {
            return false;
        }

        if ($submission->stage != SubmissionStage::CallforAbstract) {
            return false;
        }

        if ($user->can('Submission:acceptAbstract')) {
            return true;
        }
    }

    public function review(User $user, Submission $submission)
    {
        if ($submission->stage != SubmissionStage::PeerReview) {
            return false;
        }

        if ($user->can('Submission:review')) {
            return true;
        }
    }

    public function requestRevision(User $user, Submission $submission)
    {
        if ($submission->revision_required) {
            return false;
        }

        if ($submission->stage != SubmissionStage::PeerReview) {
            return false;
        }

        if ($user->can('Submission:requestRevision')) {
            return true;
        }
    }

    public function skipReview(User $user, Submission $submission)
    {
        if ($submission->stage != SubmissionStage::PeerReview) {
            return false;
        }

        if ($user->can('Submission:skipReview')) {
            return true;
        }
    }

    public function assignParticipant(User $user)
    {
        if ($user->can('Submission:assignParticipant')) {
            return true;
        }
    }

    public function publish(User $user, Submission $submission)
    {
        if ($submission->status != SubmissionStatus::Editing) {
            return false;
        }

        if ($user->can('Submission::publish')) {
            return true;
        }
    }
}
