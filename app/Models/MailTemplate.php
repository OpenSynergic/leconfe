<?php

namespace App\Models;

use App\Facades\Setting;
use App\Models\Concerns\BelongsToConference;
use Illuminate\Contracts\Mail\Mailable;
use Spatie\MailTemplates\Interfaces\MailTemplateInterface;
use Spatie\MailTemplates\Models\MailTemplate as BaseMailTemplate;

class MailTemplate extends BaseMailTemplate implements MailTemplateInterface
{
    use BelongsToConference;

    public function getHtmlLayout(): string
    {
        return view('mail.template', [
            'body' => '{{{ body }}}',
            'header' => Setting::get('mail_header'),
            'footer' => Setting::get('mail_footer'),
        ])->render();
    }

    protected static function booted(): void
    {
        static::updating(function (MailTemplate $model) {
            $model->text_template = preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($model->html_template))));
        });
    }

    public static function findForMailable(Mailable $mailable): self
    {
        $mailTemplate = static::forMailable($mailable)->first();

        if (! $mailTemplate) {
           $mailTemplate = new static([
                'mailable' => get_class($mailable),
                'subject' => $mailable::getDefaultSubject(),
                'html_template' => $mailable::getDefaultHtmlTemplate(),
                'text_template' => $mailable::getDefaultTextTemplate(),
           ]);
        }

        return $mailTemplate;
    }
}
