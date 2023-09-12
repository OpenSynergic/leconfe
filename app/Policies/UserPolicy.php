<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('User:viewAny')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model)
    {
        if ($user->can('User:view')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('User:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model)
    {
        if ($user->can('User:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model)
    {
        // User can delete itselfs
        if ($user->getKey() === $model->getKey()) {
            return true;
        }

        if ($user->can('User:delete')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model)
    {
        if ($user->can('User:restore')) {
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model)
    {
        if ($user->can('User:forceDelete')) {
            return true;
        }
    }

    public function loginAs(User $user, User $model)
    {
        if ($user->getKey() === $model->getKey()) {
            return false;
        }

        if ($user->can('User:loginAs')) {
            return true;
        }
    }

    public function assignRoles(User $user)
    {
        if ($user->can('User:assignRoles')) {
            return true;
        }
    }
}
