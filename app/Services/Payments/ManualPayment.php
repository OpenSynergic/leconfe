<?php

namespace App\Services\Payments;

use App\Models\Enums\PaymentState;
use App\Models\Payment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\App;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ManualPayment extends BasePayment
{
    public function getName(): string
    {
        return 'Manual Payment';
    }

    public function pay($amount, $submission)
    {
        $submission->update([
            'payment_amount' => $amount,
            'payment_status' => 'paid',
        ]);
    }

    public function handlePayment(Payment $payment)
    {
        // Send information to Editor that user is paying the submission


        // Change payment status to processing
        $payment->state = PaymentState::Processing;
        $payment->save();
    }

    public function getPaymentFormSchema(): array
    {
        return [
            TinyEditor::make('instructions')
                ->label('Payment Instruction')
                ->disabled(),
            SpatieMediaLibraryFileUpload::make('payment_proof')
                ->collection('payment_proof')
                ->label('Payment Proof')
                ->required()
                ->downloadable(),
        ];
    }

    public function getPaymentFormFill(): array
    {
        $conference = App::getCurrentConference();

        return [
            'instructions' => $conference->getMeta('manual_payment.instructions'),
        ];
    }

    public function getSettingFormSchema(): array
    {
        return [
            TinyEditor::make('manual.instructions')
                ->label('Payment Instruction')
                ->required(),
        ];
    }

    public function getSettingFormFill(): array
    {
        $conference = App::getCurrentConference();

        return [
            'instructions' => $conference->getMeta('manual_payment.instructions'),
        ];
    }

    public function saveSetting(array $data): void
    {
        $conference = App::getCurrentConference();

        $conference->setManyMeta([
            'manual_payment.instructions' => data_get($data, 'instructions'),
        ]);
    }
}
