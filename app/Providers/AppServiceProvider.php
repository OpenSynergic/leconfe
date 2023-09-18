<?php

namespace App\Providers;

use App\Managers\BlockManager;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('block', function () {
            return new BlockManager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->setupModel();
        $this->setupStorage();
    }

    protected function setupModel()
    {
        // As these are concerned with application correctness,
        // leave them enabled all the time.
        // Model::preventAccessingMissingAttributes();
        // Model::preventSilentlyDiscardingAttributes();

        // Since this is a performance concern only, don’t halt
        // production for violations.
        Model::preventLazyLoading(! $this->app->isProduction());
    }

    protected function setupMorph()
    {
        Relation::enforceMorphMap([
            //
        ]);
    }

    protected function setupLog()
    {
        if ($this->app->isProduction()) {
            return;
        }

        // Log a warning if we spend more than 1000ms on a single query.
        DB::listen(function ($query) {
            if ($query->time > 1000) {
                Log::warning('An individual database query exceeded 1 second.', [
                    'sql' => $query->sql,
                ]);
            }
        });

        if ($this->app->runningInConsole()) {
            // Log slow commands.
            $this->app[ConsoleKernel::class]->whenCommandLifecycleIsLongerThan(
                5000,
                function ($startedAt, $input, $status) {
                    Log::warning('A command took longer than 5 seconds.');
                }
            );
        } else {
            // Log slow requests.
            $this->app[HttpKernel::class]->whenRequestLifecycleIsLongerThan(
                5000,
                function ($startedAt, $request, $response) {
                    Log::warning('A request took longer than 5 seconds.');
                }
            );
        }
    }

    protected function setupStorage()
    {
        // Create a temporary URL for a file in the local storage disk.
        Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
            return URL::temporarySignedRoute(
                'local.temp',
                $expiration,
                array_merge($options, ['path' => $path])
            );
        });
    }

    protected function setupView()
    {
        if (!$this->app->runningInConsole()) {
            // View::share('currentConference', Conference::current());
        }
    }
}
