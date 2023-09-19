<?php

namespace App\Observers;

use App\Models\Conference;
use App\Models\Navigation;
use Illuminate\Support\Str;

class ConferenceObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Conference "created" event.
     */
    public function created(Conference $conference): void
    {
        Navigation::create([
            'name' => 'Primary Navigation Menu',
            'handle' => 'primary-navigation-menu',
            'conference_id' => $conference->getKey(),
            'items' => [
                Str::uuid()->toString() => [
                    "label" => "Home",
                    "type" => "home",
                    "data" => null,
                    "children" => []
                ],
                Str::uuid()->toString() => [
                    "label" => "Current Conference",
                    "type" => "current-conference",
                    "data" => null,
                    "children" => []
                ],
                Str::uuid()->toString() => [
                    "label" => "Announcements",
                    "type" => "announcements",
                    "data" => null,
                    "children" => []
                ],
            ],
        ]);
    }

    /**
     * Handle the Conference "updated" event.
     */
    public function updated(Conference $conference): void
    {
        //
    }

    /**
     * Handle the Conference "deleted" event.
     */
    public function deleted(Conference $conference): void
    {
        //
    }

    /**
     * Handle the Conference "restored" event.
     */
    public function restored(Conference $conference): void
    {
        //
    }

    /**
     * Handle the Conference "force deleted" event.
     */
    public function forceDeleted(Conference $conference): void
    {
        //
    }
}
