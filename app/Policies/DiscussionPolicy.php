<?php

namespace App\Policies;

use App\Models\Discussion;
use App\Models\User;

class DiscussionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->can('Discussion:viewAny')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Discussion $discussion): bool
    {
        if ($user->can('Discussion:view')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->can('Discussion:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Discussion $discussion): bool
    {
        if ($user->can('Discussion:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Discussion $discussion): bool
    {
        if ($user->can('Discussion:delete')) {
            return true;
        }
    }
}
