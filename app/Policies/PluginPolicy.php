<?php

namespace App\Policies;

use App\Models\Plugin;
use App\Models\User;

class PluginPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('Plugin:viewAny')) {
            return true;
        }
    }

    public function install(User $user)
    {
        if ($user->can('Plugin:install')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Plugin $Plugin)
    {
        if ($user->can('Plugin:delete')) {
            return true;
        }
    }
}
