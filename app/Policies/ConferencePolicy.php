<?php

namespace App\Policies;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use App\Models\User;

class ConferencePolicy
{
    public function view(User $user, Conference $conference)
    {
        if($conference->status == ConferenceStatus::Archived){
            return $user->can('Conference:viewArchived');
        }

        if($conference->status == ConferenceStatus::Upcoming){
            return $user->can('Conference:viewUpcoming');
        }

        if ($user->can('Conference:view')) {
            return true;
        }
    }

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

    public function access(User $user, Conference $conference)
    {
        if ($user->can('Conference:access')) {
            return true;
        }
    }
}
