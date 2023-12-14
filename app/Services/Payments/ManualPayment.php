<?php

namespace App\Services\Payments;

use App\Interfaces\PaymentDriver;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
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

    public function getPaymentForm(Form $form): Form
    {
        return $form->schema([
            Section::make('Payment')
                ->schema([
                    TextInput::make('payment_amount')
                        ->label('Payment Amount')
                        ->placeholder('Payment Amount')
                        ->required()
                        ->rules('required', 'numeric'),
                ]),
        ]);
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
