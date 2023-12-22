<?php

namespace App\Mail\Templates;

use App\Mail\Templates\Traits\CanCustomizeTemplate;
use App\Models\Submission;

class DeclineAbstractMail extends TemplateMailable
{
    use CanCustomizeTemplate;

    public string $authorName;

    public string $title;

    public array $logDetail;

    public function __construct(Submission $submission)
    {
        $this->authorName = $submission->user->fullName;
        $this->title = $submission->getMeta('title');

        $this->logDetail = [
            'subject_type' => $submission::class,
            'subject_id' => $submission->getKey(),
            'name' => "Abstract Declined"
        ];
    }

    public static function getDefaultSubject(): string
    {
        return 'Abstract Declined';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email template is sent when an abstract is declined.';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>Dear {{ name }},</p>
            <p>
                This is automated notification from the Leconfe System to inform you that we have declined your submission with the following title.    
            </p>
            <table>
                <tr>
                    <td style="width:100px;">Title</td>
                    <td>:</td>
                    <td>{{ title }}</td>
                </tr>
            </table>
            <p>Thank you for your interest in our conference.</p>
    HTML;
    }
}
