<?php

namespace App\Notifications;

use App\Mail\Templates\AcceptAbstractMail;
use App\Mail\Templates\NewPaymentMail;
use App\Models\Submission;
use App\Panel\Resources\SubmissionResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewPayment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Submission $submission, public array $channels = [])
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
        return (new NewPaymentMail($this->submission))->to($notifiable);
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make('new-payment')
            ->icon('lineawesome-money-check-alt-solid')
            ->iconColor('success')
            ->title('New Payment')
            ->body(function (): string {
                return "New payment for submission with id: #{$this->submission->getKey()} has been made, please verify the payment.";
            })
            ->actions([
                Action::make('view-abstract')
                    ->url(SubmissionResource::getUrl('view', ['record' => $this->submission, 'tenant' => $this->submission->conference]))
                    ->label('View')
                    ->markAsRead(),
            ])
            ->toDatabase();
    }
}
