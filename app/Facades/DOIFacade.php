<?php

namespace App\Facades;

use App\Managers\DOIManager;
use Illuminate\Support\Facades\Facade;

class DOIFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DOIManager::class;
    }
}
