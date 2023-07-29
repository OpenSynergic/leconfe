<?php

namespace App\Providers;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        //
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
        Model::preventLazyLoading(!$this->app->isProduction());

        // Relation::morphMap([
        //     'submission' => Submission::class,
        //     'author' => Author::class,
        // ]);
    }

    protected function setupStorage()
    {
        Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
            return URL::temporarySignedRoute(
                'local.temp',
                $expiration,
                array_merge($options, ['path' => $path])
            );
        });
    }
}
