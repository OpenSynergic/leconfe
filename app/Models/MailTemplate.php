<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Spatie\MailTemplates\Models\MailTemplate as BaseMailTemplate;
use Tiptap\Utils\HTML;

class MailTemplate extends BaseMailTemplate
{    
    // use BelongsToConference;

    public function getHtmlLayout(): string
    {
        return new HtmlString(<<<HTML
            <header>Site name!</header>
            {{{ body }}}
            <footer>Copyright 2018</footer>
        HTML);
        

        return $this->conference->getMeta('footer');
    }
}