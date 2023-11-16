<?php

namespace App\Mail\Templates;

use App\Mail\Templates\Traits\CanCustomizeTemplate;
use App\Models\Review;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use Carbon\Carbon;

class ReviewerInvitationMail extends TemplateMailable
{
    use CanCustomizeTemplate;

    public string $name;

    public string $submissionTitle;

    public string $dateStart;

    public string $dateEnd;

    public string $responseDeadline;

    public string $loginLink;

    public function __construct(Review $review)
    {
        $stageManager = StageManager::stage('peer-review');
        $this->name = $review->user->fullName;
        $this->submissionTitle = $review->submission->getMeta('title');

        $this->dateStart = Carbon::parse(
            $stageManager->getSetting(
                'start_at',
                now()->addDays(1)->format('d F Y')
            )
        )->format('d F Y');

        $this->dateEnd = Carbon::parse(
            $stageManager->getSetting(
                'end_at',
                now()->addDays(14)->format('d F Y')
            )
        )->format('d F Y');

        $this->responseDeadline = Carbon::parse(
            $review->created_at->addDays(
                $stageManager->getSetting('invitation_response_days', 14)
            )
        )->format('d F Y');

        $this->loginLink = route('livewirePageGroup.website.pages.login');
    }

    public static function getDefaultSubject(): string
    {
        return 'You have been assigned as a reviewer';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to reviewers when they are assigned to a submission';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>Dear {{ name }},</p>
            <p>This is an automated notification from the Leconfe System to inform you that you have been assigned as a reviewer for the following submission:</p>
            <table>
                <tr>
                    <td style="width:100px;">Title</td>
                    <td>:</td>
                    <td>{{ submissionTitle }}</td>
                </tr>
            </table>
            And here is the review details:
            <table>
                <tr>
                    <td style="width:100px;">Date Start</td>
                    <td>:</td>
                    <td>{{ dateStart }}</td>
                </tr>
                <tr>
                    <td style="width:100px;">Date End</td>
                    <td>:</td>
                    <td>{{ dateEnd }}</td>
                </tr>
                <tr>
                    <td style="width:100px;">Response Deadline</td>
                    <td>:</td>
                    <td>{{ responseDeadline }}</td>
                </tr>
            <p>Please <a href="{{ loginLink }}"> log in</a> to the system to proceed with the evaluation process.</p>
        HTML;
    }
}
