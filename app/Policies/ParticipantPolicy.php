<?php

namespace App\Policies;

use App\Models\Participant;
use App\Models\User;

class ParticipantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Participant $participant)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('Participant:create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Participant $participant)
    {
        if ($user->can('Participant:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Participant $participant)
    {
        if ($user->can('Participant:delete')) {
            return true;
        }
    }

    public function assignPosition(User $user, Participant $participant)
    {
        if ($user->can('Participant:assignPosition')) {
            return true;
        }
    }
}
