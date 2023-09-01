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
    public function viewAny(User $user): bool
    {
        return $user->can('Role:viewAny');
    }
    
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('Role:view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Role:create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        if(in_array($role->name, UserRole::values())){
            return false;
        }

        return $user->can('Role:update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        if(in_array($role->name, UserRole::values())){
            return false;
        }
        
        return $user->can('Role:delete');
    }
}
