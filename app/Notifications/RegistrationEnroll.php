<?php

namespace App\Notifications;

use Filament\Actions\Action;
use App\Mail\Templates\RegistrationEnrollMail;
use App\Models\Registration;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RegistrationEnroll extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Registration $registration)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new RegistrationEnrollMail($this->registration))
            ->to($notifiable);
    }

    public function toDatabase(object $notifiable)
    {
        $registrationCost = moneyOrFree($this->registration->registrationPayment->cost, $this->registration->registrationPayment->currency, true);

        return FilamentNotification::make()
            ->title('Registration')
            ->body("
                You've been enrolled to {$this->registration->scheduledConference->title},<br>
                Type: {$this->registration->registrationPayment->name}<br>
                Cost: {$registrationCost}<br>
                Status: {$this->registration->registrationPayment->state}
            ")
            ->actions([
                Action::make('registration-status')
                    ->label('Registration Status')
                    ->url(fn () => route('livewirePageGroup.scheduledConference.pages.registration-status', [
                        'conference' => $this->registration->scheduledConference->conference,
                        'serie' => $this->registration->scheduledConference->path,
                    ])),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
