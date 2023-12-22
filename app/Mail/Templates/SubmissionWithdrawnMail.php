<?php

namespace App\Mail\Templates;

use App\Models\Submission;

class SubmissionWithdrawnMail extends TemplateMailable
{
    public string $title;

    public array $logDetail;

    public function __construct(Submission $submission)
    {
        $this->title = $submission->getMeta('title');

        $this->logDetail = [
            'subject_type' => $submission::class,
            'subject_id' => $submission->getKey(),
            'name' => $this->getDefaultSubject()
        ];
    }

    public static function getDefaultSubject(): string
    {
        return 'Submission Withdrawn';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to authors when their submission is withdrawn';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>This is a automatic notification to let you know that your submission titled "{{ title }}" has been withdrawn.</p>
        HTML;
    }
}
