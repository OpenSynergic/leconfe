<?php

namespace Rahmanramsi\LivewirePageGroup\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rahmanramsi\LivewirePageGroup\LivewirePageGroup
 */
class LivewirePageGroup extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'livewire-page-group';
    }
}
