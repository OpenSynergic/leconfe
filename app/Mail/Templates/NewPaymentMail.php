<?php

namespace App\Mail\Templates;

use App\Classes\Log;
use App\Mail\Templates\Traits\CanCustomizeTemplate;
use App\Models\Submission;

class NewPaymentMail extends TemplateMailable
{
    use CanCustomizeTemplate;

    public string $title;

    public string $author;

    public string $items;

    public string $loginLink;

    public Log $log;

    public function __construct(Submission $submission)
    {
        $this->title = $submission->getMeta('title');
        $this->author = $submission->user->fullName;

        $this->items = "<ul style='margin:0;'>";

        foreach ($submission->payment->getMeta('items') as $item) {
            $this->items .= "<li>$item</li>";
        }

        $this->items .= '</ul>';

        $this->loginLink = route('livewirePageGroup.website.pages.login');

        $this->log = Log::make(
            name: 'email',
            subject: $submission,
            description: __('log.email.sent', ['name' => 'Abstract Accepted']),
        );

        $this->loginLink = route('livewirePageGroup.website.pages.login');

        $this->log = Log::make(
            name: 'email',
            subject: $submission,
            description: __('log.email.sent', ['name' => 'New Payment']),
        );
    }

    public static function getDefaultSubject(): string
    {
        return 'New Payment has been made';
    }

    public static function getDefaultDescription(): string
    {
        return 'This is an automated System notification informing you that a new payment has been made.';
    }

    public static function getDefaultHtmlTemplate(): string
    {

        return <<<'HTML'
            <p> This is an automated System notification informing you that a new payment has been made.s</p>
            <p>
                Payment Details:
            </p>
            <table>
                <tr>
                    <td style="width:100px;">Title</td>
                    <td>:</td>
                    <td>{{ title }}</td>
                </tr>
                <tr>
                    <td style="width:100px;">Author</td>
                    <td>:</td>
                    <td>{{ author }}</td>
                </tr>
                <tr>
                    <td style="width:100px;vertical-align:top;">Items</td>
                    <td style="vertical-align:top;">:</td>
                    <td>{{{ items }}}</td>
                </tr>
            </table>
            <p>A new payment has been made. To confirm the payment, please log in to the system.<a href="{{ loginLink }}">Click here</a> to login to your account.</p>
        HTML;
    }
}
