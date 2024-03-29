<?php

namespace App\Mail\Templates;

use App\Classes\Log;
use App\Mail\Templates\Traits\CanCustomizeTemplate;
use App\Models\Presenter;
use App\Models\Submission;

class RejectedPresenterMail extends TemplateMailable
{
    use CanCustomizeTemplate;

    public string $presenter;
    
    public string $title;

    public string $loginLink;

    public ?string $message;

    public Log $log;

    public function __construct(protected Presenter $presenterModel)
    {
        $this->presenter = $presenterModel->fullName;
        $this->title = $presenterModel->submission->getMeta('title');
        $this->loginLink = route('filament.conference.pages.dashboard', $presenterModel->submission->conference);

        $this->log = Log::make(
            name: 'email',
            subject: $presenterModel->submission,
            description: __('log.email.sent', ['name' => 'Rejected Presenter']),
        );

        $this->message = (string) $this->buildView()['text'];
    }

    public static function getDefaultSubject(): string
    {
        return 'You have been rejected as a presenter';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to presenters when they are rejected from a submission';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>
                This is a automatic notification to let you know that {{ presenter }} has been rejected from the submission titled "{{ title }}".
                You can <a href="{{ loginLink }}">log in</a> to the system to see the details.
            </p>
        HTML;
    }
}
