<?php

namespace App\Notifications;

use App\Mail\Templates\PaymentSentMail;
use App\Models\Submission;
use App\Panel\Conference\Resources\SubmissionResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentSent extends Notification implements ShouldQueue
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
        return (new PaymentSentMail($this->submission))->to($notifiable);
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make('payment-success')
            ->icon('lineawesome-check-circle')
            ->iconColor('success')
            ->title('Payment Success')
            ->body(function (): string {
                return "Payment for submission with id: #{$this->submission->getKey()} is successfull, please wait for the admin to verify your payment.";
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
