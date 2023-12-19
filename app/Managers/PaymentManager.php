<?php

namespace App\Managers;

use App\Models\Interfaces\HasPayment;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\ManualPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Manager;

class PaymentManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return App::getCurrentConference()?->getMeta('workflow.payment.payment_method') ?? 'manual';
    }

    public function createManualDriver()
    {
        return new ManualPayment;
    }

    public function createPayment(Model $model, User $user, float $amount, string $currencyId, ?string $paymentMethod = null)
    {
        $payment = $model->payment ?? new Payment;
        $payment->amount = $amount;
        $payment->currency_id = $currencyId;
        $payment->payment_method = $paymentMethod ?? $this->getDefaultDriver();
        if(!$payment->exists){
            $payment->user()->associate($user);
            $payment->payable()->associate($model);
        }
        $payment->save();

        return $payment;
    }

    public function getAllDriverNames()
    {
        return collect(['manual', ...$this->customCreators])->mapWithKeys(function ($driver) {
            return [$driver => $this->driver($driver)->getName()];
        });
    }
}
