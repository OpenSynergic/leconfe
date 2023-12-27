<?php

namespace App\Mail\Templates;

use App\Classes\Log;
use App\Mail\Templates\Traits\CanCustomizeTemplate;
use App\Models\Payment;

class PaymentStatusUpdatedMail extends TemplateMailable
{
    use CanCustomizeTemplate;

    public string $loginLink;

    public string $appName;

    public string $statusPayment;

    public Log $log;

    public function __construct(Payment $payment)
    {
        $this->appName = config('app.name');
        $this->statusPayment = $payment->state->getLabel();
        $this->loginLink = route('livewirePageGroup.website.pages.login');

        $this->log = Log::make(
            name: 'email',
            subject: $payment->payable,
            description: __('log.email.sent', ['name' => 'Payment Status Updated']),
        );
    }

    public static function getDefaultSubject(): string
    {
        return 'Payment Status Updated';
    }

    public static function getDefaultDescription(): string
    {
        return 'This is an automated notification from System to inform you about a your payment status.';
    }

    public static function getDefaultHtmlTemplate(): string
    {

        return <<<'HTML'
        <p> This is an automated notification from the {{ appName }} System to inform you about a your payment status.</p>
        <p>
            Payment Details: 
        </p>
        <p>Your payment status was chaged to <strong>{{ statusPayment }}</strong> by editor team</p>
        <p>
            Please <a href="{{ loginLink }}">login</a> to your account to see the details.
        </p>
        HTML;
    }
}
