<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;

class LogSentEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }


    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {

        if (!isset($event->data['log'])) return;

        $log = $event->data['log'];

        activity($log->name)
            ->byAnonymous()
            ->performedOn($log->subject)
            ->log($log->description);
    }
}
