<?php

namespace App\Providers;

use App\Facades\Payment;
use App\Managers\BlockManager;
use App\Managers\MetaTagManager;
use App\Services\Payments\PaypalPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

    protected function detectConference()
    {
        if (! $this->app->isInstalled()) {
            return;
        }

        $this->app->scopeCurrentConference();

        $pathInfos = explode('/', request()->getPathInfo());

        // Special case for `current` path
        if (isset($pathInfos[1]) && ! blank($pathInfos[1])) {
            $conferenceId = DB::table('conferences')->where('path', $pathInfos[1])->value('id');
            if (! $conferenceId) {
                // Conference not found
                $this->app->setCurrentConferenceId(0);

                return;
            }

            $this->app->setCurrentConferenceId($conferenceId);

            return;
        }

    }
}
