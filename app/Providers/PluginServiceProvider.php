<?php

namespace App\Providers;

use App\Http\Middleware\DetectConferenceContext;
use App\Managers\PluginManager;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->scoped('plugin', function (): PluginManager {
            $manager = new PluginManager;
            $this->initializePluginManager($manager, request(), app()->runningInConsole());

            return $manager;
        });

    }

    protected function initializePluginManager(PluginManager $manager, Request $request, bool $runningInConsole): void
    {
        $manager->registerCorePlugins();

        if (! $runningInConsole && ! isset($_SERVER['LARAVEL_OCTANE'])) {
            app(DetectConferenceContext::class)->detect($request);
        }

        $manager->initialize();
    }
}
