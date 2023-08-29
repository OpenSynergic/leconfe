<?php

namespace App\Policies;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use App\Models\User;

class ConferencePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Conference $conference): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Conference:create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Conference $conference): bool
    {
        if (! in_array($conference->status, [ConferenceStatus::Current, ConferenceStatus::Upcoming])) {
            return false;
        }

        return $user->can('Conference:update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Conference $conference): bool
    {
        if (in_array($conference->status, [ConferenceStatus::Current, ConferenceStatus::Archived])) {
            return false;
        }

        return $user->can('Conference:delete');
    }

    public function setAsCurrent(User $user, Conference $conference): bool
    {
        if ($conference->status != ConferenceStatus::Upcoming) {
            return false;
        }

        return $user->can('Conference:setAsCurrent');
    }
}
