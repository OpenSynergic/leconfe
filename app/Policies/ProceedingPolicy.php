<?php

namespace App\Policies;

use App\Models\Proceeding;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProceedingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if($user->can('Proceeding:viewAny')){
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proceeding $proceeding)
    {
        if($user->can('Proceeding:view')){
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if($user->can('Proceeding:create')){
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proceeding $proceeding)
    {
        if($user->can('Proceeding:update')){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proceeding $proceeding)
    {
        if($user->can('Proceeding:delete')){
            return true;
        }
    }
}
