<?php

namespace App\Actions\MailTemplates;

use App\Models\MailTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class MailTemplateRestoreDefaultData
{
    use AsAction;

    public function handle(MailTemplate $mailTemplate) : void
    {
        $class = $mailTemplate->mailable;

        $data = [
            'subject' => $class::getDefaultSubject(),
            'html_template' => $class::getDefaultHtmlTemplate(),
            'text_template' => $class::getDefaultTextTemplate(),
            'description' => $class::getDefaultDescription(),
        ];

        MailTemplate::updateOrCreate(
            ['mailable' => $class],
            $data
        );
    }
}
