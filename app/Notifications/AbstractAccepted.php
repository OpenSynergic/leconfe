<?php

namespace App\Notifications;

use App\Models\Submission;
use App\Panel\Resources\SubmissionResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AbstractAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Submission $submission)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make('abstract-accepted')
            ->title("Abstract Accepted")
            ->body("Title: {$this->submission->getMeta('title')}")
            ->actions([
                Action::make('view-abstract')
                    ->url(SubmissionResource::getUrl('view', ['record' => $this->submission]))
                    ->label("View")
            ])
            ->toDatabase();
    }
}
