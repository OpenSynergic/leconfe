<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Classes\Plugin getPlugin(string $pluginName, bool $onlyEnabled = false)
 * @method static \Illuminate\Support\Collection getPlugins()
 * @method static \Illuminate\Support\Collection getPluginsForTelemetry()
 */
class Plugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'plugin';
    }
}
