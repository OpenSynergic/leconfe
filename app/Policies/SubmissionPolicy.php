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
        if (!StageManager::callForAbstract()->isStageOpen()) {
            return false;
        }

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
        if ($user->can('Submission:viewAny')) {
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

    public function review(User $user, Submission $submission)
    {
        if ($submission->stage != SubmissionStage::PeerReview) {
            return false;
        }

        if ($user->can('Submission:review')) {
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
