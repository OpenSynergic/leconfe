<?php

namespace App\Policies;

use App\Models\NavigationMenu;
use App\Models\User;

class NavigationMenuPolicy
{
    public function viewAny(User $user)
    {
        if ($user->can('NavigationMenu:viewAny')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('NavigationMenu:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NavigationMenu $navigation)
    {
        if ($user->can('NavigationMenu:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NavigationMenu $navigation)
    {
        if ($user->can('NavigationMenu:delete')) {
            return true;
        }
    }
}
