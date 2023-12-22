<?php

namespace App\Mail\Templates;

use App\Models\Review;

class ReviewerDeclinedInvitationMail extends TemplateMailable
{
    public string $reviewer;

    public string $submissionTitle;

    public array $logDetail;

    public function __construct(Review $review)
    {
        $this->reviewer = $review->user->fullName;
        $this->submissionTitle = $review->submission->getMeta('title');

        $this->logDetail = [
            'subject_type' => $review->submission::class,
            'subject_id' => $review->submission->getKey(),
            'name' => "Reviewer Declined Invitation"
        ];
    }

    public static function getDefaultSubject(): string
    {
        return 'Reviewer Declined Invitation';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to reviewers when they decline the invitation to review a submission';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>This is a automatic notification to let you know that {{ reviewer }} has declined the invitation to review the submission titled "{{ submissionTitle }}".</p>
        HTML;
    }
}
