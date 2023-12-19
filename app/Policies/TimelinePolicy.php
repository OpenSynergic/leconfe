<?php

namespace App\Policies;

use App\Models\Timeline;
use App\Models\User;

class TimelinePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('Timeline:viewAny')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Timeline $timeline)
    {
        if ($user->can('Timeline:view')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('Timeline:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Timeline $timeline)
    {
        if ($user->can('Timeline:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Timeline $timeline)
    {
        if ($user->can('Timeline:delete')) {
            return true;
        }
    }
}
