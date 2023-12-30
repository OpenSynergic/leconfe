<?php

namespace App\Providers;

use App\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelIgnition\Facades\Flare;

class FlareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Flare::determineVersionUsing(function() {
            return Application::APP_VERSION;
        });
        Flare::group('Informations', [
            'url' => url(),
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            if(!setting('send-error-report')){
                Config::set('logging.channels.stack.channels', ['daily']);
            }
        } catch (\Throwable $th) {
            // 
        }

    }
}
