<?php

namespace App\Observers;

use App\Actions\Series\CommitteeRolePopulateDefaultDataAction;
use App\Models\Serie;

class SerieObserver
{
    /**
     * Handle the Serie "created" event.
     */
    public $afterCommit = true;
    
    public function created(Serie $serie): void
    {
        CommitteeRolePopulateDefaultDataAction::run($serie);
    }

    /**
     * Handle the Serie "updated" event.
     */
    public function updated(Serie $serie): void
    {
        //
    }

    /**
     * Handle the Serie "deleted" event.
     */
    public function deleted(Serie $serie): void
    {
        //
    }

    /**
     * Handle the Serie "restored" event.
     */
    public function restored(Serie $serie): void
    {
        //
    }

    /**
     * Handle the Serie "force deleted" event.
     */
    public function forceDeleted(Serie $serie): void
    {
        //
    }
}
