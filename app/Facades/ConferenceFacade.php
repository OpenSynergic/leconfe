<?php

namespace App\Facades;

use App\Managers\ConferenceManager;
use App\Managers\DOIManager;
use Illuminate\Support\Facades\Facade;

class ConferenceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ConferenceManager::class;
    }
}
