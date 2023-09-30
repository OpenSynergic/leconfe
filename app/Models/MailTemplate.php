<?php

namespace App\Models;

use Spatie\MailTemplates\Models\MailTemplate as BaseMailTemplate;

class MailTemplate extends BaseMailTemplate
{
    // use BelongsToConference;

    public function getHtmlLayout(): string
    {
        return view('mail.template', [
            'body' => '{{{ body }}}',
            'header'=> setting('mail.header'),
            'footer'=> setting('mail.footer'),
        ])->render();
    }

    protected static function booted(): void
    {
        static::updating(function (MailTemplate $model) {
            $model->text_template = preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($model->html_template))));
        });
    }
}
