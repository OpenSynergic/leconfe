<?php

namespace App\Providers;

use App\Application;
use App\Classes\Setting;
use App\Facades\SidebarFacade;
use App\Models\Serie;
use Livewire\Livewire;
use App\Classes\Settings;
use App\Facades\ConferenceFacade;
use App\Listeners\SubmissionEventSubscriber;
use App\Models\Conference;
use Illuminate\Support\Str;
use App\Managers\BlockManager;
use App\Managers\ConferenceManager;
use App\Managers\DOIManager;
use App\Managers\MetaTagManager;
use App\Managers\SidebarManager;
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
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(SidebarManager::class, function () {
            return new SidebarManager;
        });

        $this->app->scoped('metatag', function () {
            return new MetaTagManager;
        });

        $this->app->scoped(DOIManager::class, function () {
            return new DOIManager;
        });

        $this->app->scoped(ConferenceManager::class, function () {
            return new ConferenceManager;
        });

        $this->app->bind(Setting::class, function ($app) {
            return new Setting();
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


        Event::subscribe(SubmissionEventSubscriber::class);
    }

    protected function extendStr()
    {
        /**
         * Add macro to Str class to mask email address.
         */
        Str::macro('maskEmail', function ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }

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
    
        $conference = ConferenceFacade::getCurrentConference();
        if($conference){
            Livewire::setUpdateRoute(
                fn ($handle) => Route::post('{conference:path}/series/{serie:path}/livewire/update', $handle)->middleware('web')
            );
        }
    }
}
