<?php

namespace Rahmanramsi\LaravelAsyncQueue;

use Illuminate\Queue\SyncQueue;
use Throwable;

use function Amp\async;

class AsyncQueue extends SyncQueue
{
    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     *
     * @throws \Throwable
     */
    public function push($job, $data = '', $queue = null)
    {
        async(function () use ($job, $data, $queue) {
            $queueJob = $this->resolveJob($this->createPayload($job, $queue, $data), $queue);
            try {
                $this->raiseBeforeJobEvent($queueJob);

                $queueJob->fire();

                $this->raiseAfterJobEvent($queueJob);
            } catch (Throwable $e) {
                $this->handleException($queueJob, $e);
            }
        });

        return 0;
    }

    /**
     * Resolve a Sync job instance.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @return \Illuminate\Queue\Jobs\SyncJob
     */
    protected function resolveJob($payload, $queue)
    {
        return new AsyncJob($this->container, $payload, $this->connectionName, $queue);
    }
}
