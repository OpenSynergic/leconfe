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
        if (!isset($event->data['logDetail'])) return;

        $subject = (new $event->data['logDetail']['subject_type'])
            ->find($event->data['logDetail']['subject_id']);

        $recipients = collect($event->message->getTo())
            ->join(fn ($recipient) => $recipient->getAddress() . ':');

        if ($subject) {
            activity('email')
                ->byAnonymous()
                ->performedOn($subject)
                ->log(
                    __('log.email.sent', [
                        'name' => $event->data['logDetail']['name'],
                        'email' => $recipients,
                    ])
                );
        }
    }
}
