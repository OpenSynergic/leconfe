<?php

namespace App\Managers;

use App\Interfaces\PaymentDriver;
use App\Models\Interfaces\HasPayment;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\User;
use App\Services\Payments\ManualPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Manager;

class PaymentManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return App::getCurrentConference()?->getMeta('workflow.payment.payment_method') ?? 'manual';
    }

    public function createManualDriver() : PaymentDriver
    {
        return new ManualPayment;
    }

    public function createPayment(Model $model, User $user, string $currencyId, array $items, ?string $paymentMethod = null) : Payment
    {
        try {
            DB::beginTransaction();

            $items = PaymentItem::whereIn('id', $items)->get();
            
            $payment = $model->payment ?? new Payment;
            $payment->amount = $items->sum(fn($item) => $item->getAmount($currencyId));
            $payment->currency_id = $currencyId;
            $payment->payment_method = $paymentMethod ?? $this->getDefaultDriver();
            if(!$payment->exists){
                $payment->user()->associate($user);
                $payment->payable()->associate($model);
            }
            $payment->save();

            $payment->setMeta('items', $items->map(fn($item) => $item->name . ': ' . $item->getFormattedAmount($currencyId)));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }


        return $payment;
    }

    public function getAllDriverNames() : \Illuminate\Support\Collection
    {
        return collect(['manual', ...$this->customCreators])->mapWithKeys(function ($driver) {
            return [$driver => $this->driver($driver)->getName()];
        });
    }
}
