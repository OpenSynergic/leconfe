<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getPlugin(string $pluginName)
 * @method static string getPlugins()
 */
class Plugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'plugin';
    }
}
