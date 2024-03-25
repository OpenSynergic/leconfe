<?php

namespace App\Policies;

use App\Models\Serie;
use App\Models\User;

class SeriePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('Serie:viewAny')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Serie $serie)
    {
        if ($user->can('Serie:view')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('Serie:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Serie $serie)
    {
        if ($user->can('Serie:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Serie $serie)
    {
        if ($user->can('Serie:delete')) {
            return true;
        }
    }
}
