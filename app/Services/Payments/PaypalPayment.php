<?php

namespace App\Services\Payments;

use App\Models\Enums\PaymentState;
use App\Models\Payment;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\App;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PaypalPayment extends BasePayment
{
    public function getName(): string
    {
        return 'Paypal Payment';
    }

    public function handlePayment(Payment $payment)
    {
        $payment->state = PaymentState::Paid;
        $payment->paid_at = now();
        $payment->save();
    }

    public function getPaymentFormSchema(): array
    {
        return [];
    }

    public function getPaymentFormFill(): array
    {
        return [];
    }

    public function getSettingFormSchema(): array
    {
        return [];
    }

    public function getSettingFormFill(): array
    {
        return [];
    }

    public function saveSetting(array $data): void
    {
    }
}
