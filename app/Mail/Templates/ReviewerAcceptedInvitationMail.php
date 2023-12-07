<?php

namespace App\Mail\Templates;

use App\Models\Review;

class ReviewerAcceptedInvitationMail extends TemplateMailable
{
    public string $reviewer;

    public string $submissionTitle;

    public function __construct(Review $review)
    {
        $this->reviewer = $review->user->fullName;
        $this->submissionTitle = $review->submission->getMeta('title');
    }

    public static function getDefaultSubject(): string
    {
        return 'Reviewer Accepted Invitation';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to reviewers when they accept the invitation to review a submission';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>This is a automatic notification to let you know that {{ reviewer }} has accepted the invitation to review the submission titled "{{ submissionTitle }}".</p>
        HTML;
    }
}
