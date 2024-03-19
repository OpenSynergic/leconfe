<?php

namespace App\Notifications;

use App\Mail\Templates\PaymentStatusUpdatedMail;
use App\Models\Enums\PaymentType;
use App\Models\Payment;
use App\Panel\Conference\Resources\SubmissionResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment,
        public array $channels = [],
    ) {
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
        return (new PaymentStatusUpdatedMail($this->payment))->to($notifiable);
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make('payment-status-upadted')
            ->icon('lineawesome-money-check-alt-solid')
            ->iconColor('success')
            ->title('Payment Status Updated')
            ->actions(function (): array {
                if ($this->payment->type == PaymentType::Submission) {
                    return [
                        Action::make('view-abstract')
                            ->url(SubmissionResource::getUrl('view', [
                                'record' => $this->payment->payable_id,
                                'tenant' => $this->payment->payable->conference,
                            ]))
                            ->label('View')
                            ->markAsRead(),
                    ];
                }

                return [];
            })
            ->toDatabase();
    }
}
