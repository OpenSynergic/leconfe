<?php

namespace App\Providers;

use App\Application;
use App\Models\Serie;
use Livewire\Livewire;
use App\Classes\Settings;
use App\Models\Conference;
use Illuminate\Support\Str;
use App\Managers\BlockManager;
use App\Managers\MetaTagManager;
use Illuminate\Support\Facades\DB;
use App\Routing\CustomUrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped('block', function () {
            return new BlockManager;
        });

        $this->app->scoped('metatag', function () {
            return new MetaTagManager;
        });

        // Use a custom URL generator to accomodate multi context.
        // This implementation is copied from Illuminate\Routing\RoutingServiceProvider::registerUrlGenerator
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            return new CustomUrlGenerator(
                $routes,
                $app->rebinding(
                    'request',
                    function ($app, $request) {
                        $app['url']->setRequest($request);
                    }
                ),
                $app['config']['app.asset_url']
            );
        });

        $this->app->bind('Settings', function ($app) {
            return new Settings();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->setupModel();
        $this->setupStorage();
        $this->extendStr();
        $this->detectConference();
    }

    protected function extendStr()
    {
        Str::macro('maskEmail', function ($email) {
            $mail_parts = explode('@', $email);
            $domain_parts = explode('.', $mail_parts[1]);

            $mail_parts[0] = Str::mask($mail_parts[0], '*', 2, strlen($mail_parts[0])); // show first 2 letters and last 1 letter
            $domain_parts[0] = Str::mask($domain_parts[0], '*', 2, strlen($domain_parts[0])); // same here
            $mail_parts[1] = implode('.', $domain_parts);

            return implode('@', $mail_parts);
        });
    }

    protected function setupModel()
    {
        // As these are concerned with application correctness,
        // leave them enabled all the time.
        // Model::preventAccessingMissingAttributes();
        // Model::preventSilentlyDiscardingAttributes();

        // Since this is a performance concern only, don’t halt
        // production for violations.
        Model::preventLazyLoading(!$this->app->isProduction());
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

    protected function detectConference()
    {
        if ($this->app->runningInConsole() || !$this->app->isInstalled()) {
            return;
        }

        $pathInfos = explode('/', request()->getPathInfo());
        // Detect conference from URL path
        if (isset($pathInfos[1]) && !blank($pathInfos[1])) {

            $conference = Conference::where('path', $pathInfos[1])->first();

            $conference ? $this->app->setCurrentConferenceId($conference->getKey()) : $this->app->setCurrentConferenceId(Application::CONTEXT_WEBSITE);

            // Detect serie from URL path
            if (isset($pathInfos[3]) && !blank($pathInfos[3])) {
                $serie = Serie::where('path', $pathInfos[3])->first();
                $serie && $this->app->setCurrentSerieId($serie->getKey());
            }
        }

        // Scope livewire update path to current conference
        $currentConference = $this->app->getCurrentConference();
        if ($currentConference) {
            $this->app->scopeCurrentConference();
            // Scope livewire update path to current serie
            $currentSerie = $this->app->getCurrentSerie();
            if ($currentSerie) {
                $this->app->scopeCurrentSerie();
                Livewire::setUpdateRoute(
                    fn ($handle) => Route::post($currentConference->path . '/series/' . $currentSerie->path . '/livewire/update', $handle)->middleware('web')
                );
            } else {
                Livewire::setUpdateRoute(fn ($handle) => Route::post($currentConference->path . '/livewire/update', $handle)->middleware('web'));
            }
            setPermissionsTeamId($this->app->getCurrentConferenceId());
        }
    }
}
