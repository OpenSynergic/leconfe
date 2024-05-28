<?php

namespace App\Policies;

use App\Models\Presenter;
use App\Models\User;

class PresenterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if($user->can('Presenter:viewAny')){
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Presenter $presenter)
    {
        if($user->can('Presenter:view')){
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if($user->can('Presenter:create')){
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Presenter $presenter)
    {
        if($user->can('Presenter:update')){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Presenter $presenter)
    {
        if($user->can('Presenter:delete')){
            return true;
        }
    }
}
