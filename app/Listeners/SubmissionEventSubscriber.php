<?php

namespace App\Listeners;

use App\Events\Submissions\Accepted;
use App\Events\Submissions\Published;
use App\Facades\DOIFacade;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class SubmissionEventSubscriber
{
    public function generateDoiUponReachingEditingStage(Accepted $event)
    {
        $doiEnabled = app()->getCurrentConference()->getMeta('doi_enabled');
        $isAutomaticAssignmentUponReachingEditingStage = app()->getCurrentConference()->getMeta('doi_automatic_assignment') == 'edit_stage';
        $doiFormat = app()->getCurrentConference()->getMeta('doi_format');

        if (!$doiEnabled) return;
        if (!$isAutomaticAssignmentUponReachingEditingStage) return;
        if ($doiFormat === 'none') return;

        if (!$event->submission->doi) {
            $event->submission
                ->doi()
                ->create(['doi' => DOIFacade::generate()]);
        }
    }

    public function generateDoiUponPublication(Published $event)
    {
        $doiEnabled = app()->getCurrentConference()->getMeta('doi_enabled');
        $isAutomaticAssignmentUponPublication = app()->getCurrentConference()->getMeta('doi_automatic_assignment') == 'published';
        $doiFormat = app()->getCurrentConference()->getMeta('doi_format');

        if (!$doiEnabled) return;
        if (!$isAutomaticAssignmentUponPublication) return;
        if ($doiFormat === 'none') return;

        if (!$event->submission->doi) {
            $event->submission
                ->doi()
                ->create(['doi' => DOIFacade::generate()]);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Accepted::class => 'generateDoiUponReachingEditingStage',
            Published::class => 'generateDoiUponPublication',
        ];
    }
}
