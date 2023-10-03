<?php

namespace App\Mail\Templates;

class TestMail extends TemplateMailable
{
    public static function getDefaultSubject(): string
    {
        return 'Test Mail';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
        This is a test mail from Leconfe
        HTML;
    }

    public static function getDefaultDescription(): string
    {
        return 'Email template for testing purposes';
    }
}
