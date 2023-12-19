<?php

namespace App\Interfaces;

use App\Models\Payment;
use Filament\Forms\Form;

interface PaymentDriver
{
    public function getName(): string;

    public function pay($amount, $submission);

    public function getPaymentFormSchema(): array;

    public function getSettingFormSchema(): array;

    public function getSettingFormFill(): array;

    public function saveSetting(array $data): void;

    public function handlePayment(Payment $payment);

    // public function fillSettingForm(Form $form): void;
}
