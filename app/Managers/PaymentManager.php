<?php

namespace App\Managers;

use App\Models\Enums\PaymentType;
use App\Services\Payments\ManualPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Manager;

class PaymentManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'manual';
    }

    public function createManualDriver()
    {
        return new ManualPayment;
    }

    public function createPayment(PaymentType $type, Model $model)
    {
    }

    public function getAllDriverNames()
    {
        return collect(['manual', ...$this->customCreators])->mapWithKeys(function ($driver) {
            return [$driver => $this->driver($driver)->getName()];
        });
    }
}
