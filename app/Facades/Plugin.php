<?php

namespace App\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Classes\Plugin getPlugin(string $pluginName, bool $onlyEnabled = false)
 * @method static Collection getPlugins()
 */
class Plugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'plugin';
    }
}
