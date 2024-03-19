<?php

namespace App\Observers;

use App\Models\Site;

class SiteObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Site "created" event.
     */
    public function created(Site $site): void
    {

    }

    /**
     * Handle the Site "updated" event.
     */
    public function updated(Site $site): void
    {
        //
    }

    /**
     * Handle the Site "deleted" event.
     */
    public function deleted(Site $site): void
    {
        //
    }

    /**
     * Handle the Site "deleted" event.
     */
    public function deleting(Site $site): void
    {
    }

    /**
     * Handle the Site "restored" event.
     */
    public function restored(Site $site): void
    {
        //
    }

    /**
     * Handle the Site "force deleted" event.
     */
    public function forceDeleted(Site $site): void
    {
        //
    }
}
