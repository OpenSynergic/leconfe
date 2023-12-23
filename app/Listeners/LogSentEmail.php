<?php

namespace App\Listeners;

use App\Classes\Log;
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

        if (! isset($event->data['log'])) {
            return;
        }

        $log = $event->data['log'];

        if ($log instanceof Log) {
            $log->save();
        }
    }
}
