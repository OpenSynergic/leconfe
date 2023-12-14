<?php

namespace App\Interfaces;

use Filament\Forms\Form;

interface PaymentDriver
{
    public function getName() : string;

    public function pay($amount, $submission);

    public function getPaymentForm(Form $form) : Form;

    public function getSettingFormSchema() : array;
    
    public function getSettingFormFill(): array;

    public function saveSetting(array $data): void;

    // public function fillSettingForm(Form $form): void;
}