<?php

namespace App\Notifications;

use App\Models\Submission;
use App\Panel\Resources\SubmissionResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ParticipantAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Submission $submission)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make('participant-assigned')
            ->icon('lineawesome-exclamation-circle-solid')
            ->iconColor('primary')
            ->title('You have been assigned as a participant')
            ->body("Title: {$this->submission->getMeta('title')}")
            ->actions([
                Action::make('view-participant')
                    ->url(SubmissionResource::getUrl('view', ['record' => $this->submission, 'tenant' => $this->submission->conference]))
                    ->label('View')
                    ->markAsRead(),
            ])
            ->toDatabase();
    }
}
