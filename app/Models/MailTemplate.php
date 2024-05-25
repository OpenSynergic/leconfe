<?php

namespace App\Models;

use App\Facades\Setting;
use Spatie\MailTemplates\Models\MailTemplate as BaseMailTemplate;

class MailTemplate extends BaseMailTemplate
{
    // use BelongsToConference;

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
}
