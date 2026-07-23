<?php

namespace Tests\Feature;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Rahmanramsi\LaravelAsyncQueue\AsyncQueue;
use Tests\TestCase;

class TestAsyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle()
    {
        file_put_contents(storage_path('async_test.tmp'), 'SUCCESS');
    }
}

class AsyncQueueTest extends TestCase
{
    public function test_async_queue_instance()
    {
        $testFile = storage_path('async_test.tmp');
        if (file_exists($testFile)) {
            unlink($testFile);
        }

        $queue = app('queue')->connection('async');
        $this->assertInstanceOf(AsyncQueue::class, $queue);

        dispatch(new TestAsyncJob())->onConnection('async');

        // Trigger Laravel termination phase where Revolt EventLoop runs the queued async jobs
        $this->app->terminate();

        $this->assertFileExists($testFile);
        $this->assertEquals('SUCCESS', file_get_contents($testFile));

        if (file_exists($testFile)) {
            unlink($testFile);
        }
    }
}
