<?php

namespace App\Mail\Templates;

use App\Models\SubmissionFile;

/**
 * If the author has already uploaded a new paper, please notify the editor.
 */
class NewPaperUploadedMail extends TemplateMailable
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
            'name' => $this->getDefaultSubject()
        ];
    }

    public static function getDefaultSubject(): string
    {
        return 'New Paper Uploaded';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to editors when a new paper is uploaded';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>This is a automatic notification to let you know that "{{ uploader }}" has uploaded a new paper for the submission titled "{{ submissionTitle }}".</p>
        HTML;
    }
}
