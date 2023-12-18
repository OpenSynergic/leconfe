<?php

namespace App\Notifications;

use App\Mail\Templates\DeclineAbstractMail;
use App\Models\Submission;
use App\Panel\Resources\SubmissionResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AbstractDeclined extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Submission $submission, public string $message = '', public string $subject = '', public array $channels = [])
    {
    }

    public function via($notifiable): array
    {
        if (! filled($this->channels)) {
            return ['database', 'mail'];
        }

        return $this->channels;
    }

    public function toMail($notifiable)
    {
        $mailTempalte = new DeclineAbstractMail($this->submission);

        if (filled($this->subject)) {
            $mailTempalte = $mailTempalte->subjectUsing($this->subject);
        }

        if (filled($this->message)) {
            $mailTempalte = $mailTempalte->contentUsing($this->message);
        }

        return $mailTempalte
            ->to($notifiable);
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make('abstract-declined')
            ->icon('lineawesome-exclamation-circle-solid')
            ->iconColor('danger')
            ->title('Abstract Declined')
            ->body("Title: {$this->submission->getMeta('title')}")
            ->actions([
                Action::make('view-abstract')
                    ->url(SubmissionResource::getUrl('view', ['record' => $this->submission, 'tenant' => $this->submission->conference]))
                    ->label('View')
                    ->markAsRead(),
            ])
            ->toDatabase();
    }
}
