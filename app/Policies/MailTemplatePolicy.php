<?php

namespace App\Policies;

use App\Models\MailTemplate;
use App\Models\User;

class MailTemplatePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MailTemplate $mailTemplate)
    {
        if ($user->can('MailTemplate:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MailTemplate $mailTemplate)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MailTemplate $mailTemplate)
    {
        return false;
    }
}
