<?php

namespace App\Providers;

use App\Facades\Plugin;
use App\Managers\PluginManager;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{

    //  TODO : for this time plugin just work after installation was succesfull, fix it
    /**
     * Register services.
     */
    public function register(): void
    {


        $this->app->scoped('plugin', function (): PluginManager {
            return new PluginManager();
        });

        if ($this->isDatabaseConnected()) {
            Plugin::boot(); // So it runs before PanelProvider
        }
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Checking database connection.
     */

    protected function isDatabaseConnected(): bool
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $th) {
            return false;
        }

        return true;
    }
}
