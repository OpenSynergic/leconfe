<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function view(User $user, Announcement $announcement)
    {
        if ($user->can('Announcement:view')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        if ($user->can('Announcement:viewAny')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('Announcement:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $announcement)
    {
        if ($user->can('Announcement:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $announcement)
    {
        if ($user->can('Announcement:delete')) {
            return true;
        }
    }
}
