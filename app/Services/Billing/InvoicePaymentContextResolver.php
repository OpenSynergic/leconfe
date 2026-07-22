<?php

namespace App\Services\Billing;

use App\Models\Participant;
use App\Models\Payment;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class InvoicePaymentContextResolver
{
    public function submission(int $paymentId): Submission
    {
        $model = $this->resolve($paymentId, Submission::class);

        $model->loadMissing('user');

        return $model;
    }

    public function participant(int $paymentId): Participant
    {
        return $this->resolve($paymentId, Participant::class);
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $expectedModelClass
     * @return TModel
     */
    protected function resolve(int $paymentId, string $expectedModelClass): Model
    {
        $payment = Payment::withoutGlobalScopes()
            ->with(['model', 'scheduledConference.conference', 'fee'])
            ->findOrFail($paymentId);

        $model = $payment->model;

        if (! $model instanceof $expectedModelClass) {
            throw new LogicException("Payment {$paymentId} does not belong to {$expectedModelClass}.");
        }

        $payment->unsetRelation('model');
        $model->setRelation('payment', $payment);

        return $model;
    }
}
