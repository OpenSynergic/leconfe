<?php

namespace App\Policies;

use App\Models\StaticPage;
use App\Models\User;

class StaticPagePolicy
{
    public function viewAny(User $user)
    {
        return $user->can('StaticPage:viewAny');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('StaticPage:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StaticPage $staticPage)
    {
        if ($user->can('StaticPage:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StaticPage $staticPage)
    {
        if ($user->can('StaticPage:delete')) {
            return true;
        }
    }
}
