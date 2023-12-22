<?php

namespace App\Mail\Templates;

use App\Mail\Templates\Traits\CanCustomizeTemplate;
use App\Models\Submission;

class PublishSubmissionMail extends TemplateMailable
{
    use CanCustomizeTemplate;

    public string $title;

    public string $authorName;

    public string $loginLink;

    public array $logDetail;


    public function __construct(protected Submission $submission)
    {
        $this->title = $submission->getMeta('title');
        $this->authorName = $submission->user->fullName;
        $this->loginLink = route('livewirePageGroup.website.pages.login');

        $this->logDetail = [
            'subject_type' => $submission::class,
            'subject_id' => $submission->getKey(),
            'name' => $this->getDefaultSubject(),
        ];
    }

    public static function getDefaultSubject(): string
    {
        return 'Submission Published';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to authors when their submission is published';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>Dear {{ authorName }},</p>
            <p>This is an automated notification from the Leconfe System to inform you that your submission has been published.</p>
            <table>
                <tr>
                    <td style="width:100px;">Title</td>
                    <td>:</td>
                    <td>{{ title }}</td>
                </tr>
            </table>
            <p>
                You can <a href="{{ loginLink }}">log in</a> to the system to see the details of the submission.
            </p>
        HTML;
    }
}
