<?php

namespace App\Observers;

use App\Application;
use App\Models\Navigation;
use App\Models\Site;
use Illuminate\Support\Str;

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
        Navigation::create([
            'name' => 'Primary Navigation Menu',
            'handle' => 'primary-navigation-menu',
            'conference_id' => Application::CONTEXT_WEBSITE,
            'items' => [
                Str::uuid()->toString() => [
                    'label' => 'Home',
                    'type' => 'home',
                    'data' => null,
                    'children' => [],
                ],
            ],
        ]);
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
