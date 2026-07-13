<?php

namespace Rahmanramsi\LaravelAsyncQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Jobs\SyncJob;

class AsyncJob extends SyncJob
{
    /**
     * Get the name of the queue the job belongs to.
     *
     * @return string
     */
    public function getQueue()
    {
        return 'async';
    }
}
