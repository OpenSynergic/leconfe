<?php

namespace App\Mail\Templates\Interfaces;

interface HasDefaultMailVariable
{
    public static function getDefaultSubject(): string;

    public static function getDefaultHtmlTemplate(): string;

    public static function getDefaultTextTemplate(): string;

    public static function getDefaultDescription(): string;
}
