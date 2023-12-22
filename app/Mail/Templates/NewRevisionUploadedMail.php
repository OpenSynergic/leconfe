<?php

namespace App\Mail\Templates;

use App\Models\SubmissionFile;

class NewRevisionUploadedMail extends TemplateMailable
{
    public string $submissionTitle;

    public string $uploader;

    public array $logDetail;

    public function __construct(SubmissionFile $submissionFile)
    {
        $this->submissionTitle = $submissionFile->submission->getMeta('title');
        $this->uploader = $submissionFile->submission->user->fullName;

        $this->logDetail = [
            'subject_type' => $submissionFile->submission::class,
            'subject_id' => $submissionFile->submission->getKey(),
            'name' => "Revision Uploaded"
        ];
    }

    public static function getDefaultSubject(): string
    {
        return 'New Revision Uploaded';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to editors when a new revision is uploaded';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>This is a automatic notification to let you know that "{{ uploader }}" has uploaded a new revision for the submission titled "{{ submissionTitle }}".</p>
        HTML;
    }
}
