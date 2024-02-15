<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Classes\Plugin getPlugin(string $pluginName)
 * @method static \Illuminate\Support\Collection getPlugins()
 */
class Plugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'plugin';
    }
}
