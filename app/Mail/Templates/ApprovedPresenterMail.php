<?php

namespace App\Mail\Templates;

use App\Classes\Log;
use App\Mail\Templates\Traits\CanCustomizeTemplate;
use App\Models\Presenter;

class ApprovedPresenterMail extends TemplateMailable
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
        $this->loginLink = route('livewirePageGroup.website.pages.login');

        $this->log = Log::make(
            name: 'email',
            subject: $presenterModel->submission,
            description: __('log.email.sent', ['name' => 'Approved Presenter']),
        );

        $this->message = (string) $this->buildView()['text'];
    }

    public static function getDefaultSubject(): string
    {
        return 'You have been approved as a presenter';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent to presenters when they are approved from a submission';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
            <p>This is a automatic notification to let you know that {{ presenter }} has been approved from the submission titled "{{ title }}".</p>
        HTML;
    }
}
