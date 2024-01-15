<?php

namespace App\Policies;

use App\Models\Enums\UserRole;
use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('Role:viewAny')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role)
    {
        if ($user->can('Role:view')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('Role:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role)
    {
        if (in_array($role->name, UserRole::values()) && app()->isProduction()) {
            return false;
        }

        if ($user->can('Role:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role)
    {
        if (in_array($role->name, UserRole::values())) {
            return false;
        }

        if ($user->can('Role:delete')) {
            return true;
        }
    }
}
