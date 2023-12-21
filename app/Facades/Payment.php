<?php

namespace App\Facades;

use App\Managers\PaymentManager;
use Illuminate\Support\Facades\Facade;

class Payment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PaymentManager::class;
    }
}
