<?php

namespace App\Providers;

use App\Facades\Plugin;
use App\Managers\PluginManager;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->scoped('plugin', function (): PluginManager {
            return new PluginManager();
        });

        Plugin::boot();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
