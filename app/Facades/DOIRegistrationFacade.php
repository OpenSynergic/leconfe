<?php

namespace App\Facades;

use App\Managers\DOIRegistrationManager;
use Illuminate\Support\Facades\Facade;

class DOIRegistrationFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DOIRegistrationManager::class;
    }
}
