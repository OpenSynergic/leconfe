<?php

namespace App\Notifications;

use App\Mail\Templates\AcceptAbstractMail;
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

    public function __construct(public Submission $submission, public string $message = '', public string $subject = '')
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $mailTempalte = new AcceptAbstractMail($this->submission);
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
        return FilamentNotification::make('abstract-accepted')
            ->title("Abstract Accepted")
            ->body("Title: {$this->submission->getMeta('title')}")
            ->actions([
                Action::make('view-abstract')
                    ->url(SubmissionResource::getUrl('view', ['record' => $this->submission]))
                    ->label("View")
                    ->markAsRead()
            ])
            ->toDatabase();
    }
}
