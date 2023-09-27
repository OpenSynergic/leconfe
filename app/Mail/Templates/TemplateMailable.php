<?php

namespace App\Mail\Templates;

use App\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable as BaseTemplateMailable;

class TemplateMailable extends BaseTemplateMailable
{
    protected static $templateModelClass = MailTemplate::class;
    // public function __construct()
    // {
    //     if($conference = app()->getCurrentConference()){
    //         $this->viewData = [
    //             ...$this->viewData,
    //             'conferenceName' => $conference->name,
    //         ];
    //     }
    // }
}