<?php

namespace App\Providers;

use App\Shortcodes\HyperlinkShortcode;
use Illuminate\Support\ServiceProvider;
use Webwizo\Shortcodes\Facades\Shortcode;

class ShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Shortcode::register('a', HyperlinkShortcode::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
