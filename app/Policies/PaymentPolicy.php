<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment)
    {
        if ($payment->isCompleted()) {
            return false;
        }

        if ($user->can('Payment:update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payment $payment)
    {
        return false;
    }
}
