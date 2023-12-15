<?php

namespace App\Observers;

use App\Actions\Participants\ParticipantPositionPopulateDefaultDataAction;
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
        ParticipantPositionPopulateDefaultDataAction::run($conference);

        Navigation::create([
            'name' => 'Primary Navigation Menu',
            'handle' => 'primary-navigation-menu',
            'conference_id' => $conference->getKey(),
            'items' => [
                Str::uuid()->toString() => [
                    'label' => 'Home',
                    'type' => 'home',
                    'data' => null,
                    'children' => [],
                ],
                Str::uuid()->toString() => [
                    'label' => 'Announcements',
                    'type' => 'announcements',
                    'data' => null,
                    'children' => [],
                ],
                Str::uuid()->toString() => [
                    'label' => 'About',
                    'type' => 'about',
                    'data' => null,
                    'children' => [],
                ],
                Str::uuid()->toString() => [
                    'label' => 'Contact',
                    'type' => 'contact',
                    'data' => null,
                    'children' => [],
                ],
            ],
        ]);

        $conference->setMeta('page_footer', view('examples.footer')->render());
        $conference->setMeta('workflow.payment.supported_currencies', ['USD']);
        $conference->save();
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
     * Handle the Conference "deleted" event.
     */
    public function deleting(Conference $conference): void
    {
        if ($conference->getKey() == Conference::active()?->getKey()) {
            throw new \Exception('Conference cannot be deleted because it is currently active');
        }
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
