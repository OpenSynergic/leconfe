<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;

class TopicPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('Topic:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Topic $topic)
    {
        if ($user->can('Topic:view')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('Topic:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Topic $topic)
    {
        if ($user->can('Topic:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Topic $topic)
    {
        if ($user->can('Topic:delete')) {
            return true;
        }
    }
}
