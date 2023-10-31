<?php

namespace App\Mail\Templates;

use App\Models\Submission;

class RevisionRequestedMail extends TemplateMailable
{

    public string $title;

    public string $loginLink;

    public function __construct(Submission $submission)
    {
        $this->title = $submission->getMeta('title');
        $this->loginLink = route('livewirePageGroup.website.pages.login');
    }

    public static function getDefaultSubject(): string
    {
        return 'Revision Requested for {{ title }}';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to authors when their submission is requested for revision';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>Dear {{ name }},</p>
            <p>This is an automated notification from the Leconfe System to inform you that your submission has been requested for revision.</p>
            <table>
                <tr>
                    <td style="width:100px;">Title</td>
                    <td>:</td>
                    <td>{{ title }}</td>
                </tr>
            </table>
            <p>
                Please <a href="{{ loginLink }}"> log in</a> to the system to proceed with the revision process.
            </p>
        HTML;
    }
}
