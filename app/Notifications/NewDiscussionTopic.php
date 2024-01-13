<?php

namespace App\Notifications;

use App\Mail\Templates\NewDiscussionTopicMail;
use App\Models\DiscussionTopic;
use App\Panel\Resources\SubmissionResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewDiscussionTopic extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public DiscussionTopic $topic, public array $channels = [])
    {
    }

    public function via($notifiable): array
    {
        if (!filled($this->channels)) {
            return ['database', 'mail'];
        }

        return $this->channels;
    }

    public function toMail($notifiable)
    {
        return (new NewDiscussionTopicMail($this->topic))->to($notifiable);
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make('new-topic-created')
            ->icon('lineawesome-check-circle')
            ->iconColor('success')
            ->title('New Discussion Topic Created')
            ->body("Topic: {$this->topic->name}")
            ->actions([
                Action::make('view-submission')
                    ->url(SubmissionResource::getUrl('view', ['record' => $this->topic->submission->getKey(), 'tenant' => $this->topic->submission->conference]))
                    ->label('View')
                    ->markAsRead(),
            ])
            ->toDatabase();
    }
}
