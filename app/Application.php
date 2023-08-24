<?php

namespace App;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    public const APP_VERSION = '1.0.0';

    public const PHP_MIN_VERSION = '8.1';

    public function isInstalled()
    {
        return file_exists(storage_path('installed'));
    }

    public function getAppVersion()
    {
        return static::APP_VERSION;
    }

    public function getPhpMinVersion()
    {
        return static::PHP_MIN_VERSION;
    }

    public function getRequiredPhpExtensions()
    {
        return [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'xml',
        ];
    }
}
