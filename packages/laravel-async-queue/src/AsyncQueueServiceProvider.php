<?php

namespace Rahmanramsi\LaravelAsyncQueue;

use Illuminate\Support\ServiceProvider;
use Rahmanramsi\LaravelAsyncQueue\Middleware\AsyncMiddleware;

class AsyncQueueServiceProvider extends ServiceProvider
{

    public function register(): void
    {
    
    }

    public function boot(\Illuminate\Contracts\Http\Kernel $kernel): void
    {
        $manager = $this->app['queue'];
        $manager->addConnector('async', function(){
            return new AsyncConnector;
        });

        $this->app->terminating(fn() => \Revolt\EventLoop::run());
    }
}
