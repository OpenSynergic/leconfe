<?php

namespace App\Policies;

use App\Models\Navigation;
use App\Models\User;

class NavigationPolicy
{
    public function viewAny(User $user)
    {
        if ($user->can('Navigation:viewAny')) {
            return true;
        }
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('Navigation:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Navigation $navigation)
    {
        if ($user->can('Navigation:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Navigation $navigation)
    {
        if ($navigation->handle == 'primary-navigation-menu') {
            return false;
        }

        if ($user->can('Navigation:delete')) {
            return true;
        }
    }
}
