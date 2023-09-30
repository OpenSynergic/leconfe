<?php

namespace App\Mail\Templates;

use App\Models\Announcement;

class NewAnnouncementMail extends TemplateMailable
{
    public string $title;

    public string $content;

    public string $announcementUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->title = $announcement->title;
        $this->content = $announcement->getMeta('content');
        $this->announcementUrl = $announcement->getUrl();
    }

    public static function getDefaultSubject(): string
    {
        return '{{ title }}';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
        {{{  content  }}}
        <p>Click <a href="{{ announcementUrl }}">here</a> to read the full announcement.</p>
        HTML;
    }

    public static function getDefaultDescription(): string
    {
        return 'This email template is sent when a new announcement is created.';
    }
}
