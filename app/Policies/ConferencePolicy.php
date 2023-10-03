<?php

namespace App\Policies;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use App\Models\User;

class ConferencePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('Conference:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Conference $conference)
    {
        if (! in_array($conference->status, [ConferenceStatus::Active, ConferenceStatus::Upcoming])) {
            return false;
        }

        if ($user->can('Conference:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Conference $conference)
    {
        if (in_array($conference->status, [ConferenceStatus::Active, ConferenceStatus::Archived])) {
            return false;
        }

        if ($user->can('Conference:delete')) {
            return true;
        }
    }

    public function setAsActive(User $user, Conference $conference)
    {
        if ($conference->status != ConferenceStatus::Upcoming) {
            return false;
        }

        if ($user->can('Conference:setAsActive')) {
            return true;
        }
    }
}
