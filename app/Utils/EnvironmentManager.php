<?php

namespace App\Utils;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Artisan;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct($envPath = null)
    {
        $this->envPath = $envPath ?? base_path('.env');
    }

    /**
     * Get the .env path.
     *
     * @return string
     */
    public function envPath()
    {
        return $this->envPath;
    }

    public function installation($envs = []): bool
    {
        $defaultEnvs = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'true',
            'APP_URL' => url('/'),
            'APP_KEY' => 'base64:'.base64_encode(Encrypter::generateKey(config('app.cipher'))),
            'DB_CONNECTION' => config('database.default'),
            'DB_HOST' => config('database.connections.mysql.host'),
            'DB_PORT' => config('database.connections.mysql.port'),
            'DB_DATABASE' => config('database.connections.mysql.database'),
            'DB_USERNAME' => config('database.connections.mysql.username'),
            'DB_PASSWORD' => config('database.connections.mysql.password'),
            'MAIL_MAILER' => config('mail.mailers.smtp.username'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_PASSWORD' => config('mail.mailers.smtp.password'),
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.mailers.smtp.from.address'),
            'MAIL_FROM_NAME' => config('mail.mailers.smtp.from.name'),
            'MAIL_MAILER' => config('mail.default'),
            'MAX_FILE_UPLOAD_SIZE' => '5',
            'ACCEPTED_FILE_TYPES' => 'image/*,.pdf,.doc,.docx,.zip,.xls,xlsx,.odt,.txt,.xml',
        ];

        $envs = array_merge($defaultEnvs, $envs);

        // Delete existing .env file
        if (file_exists($this->envPath())) {
            copy($this->envPath(), $this->envPath().'.bak');
            unlink($this->envPath());
        }

        file_put_contents($this->envPath(), '');

        // Write the .env file
        foreach ($envs as $key => $value) {
            $this->writeFromEmptyEnv($key, $value);
        }

        // Clear config cache
        Artisan::call('config:clear');

        return true;
    }

    /**
     * Change a specific key in the .env file.
     *
     * @param  string  $key
     * @param  string  $value
     */
    public function changeEnv($key, $value)
    {
        $envFile = file_get_contents($this->envPath());

        $envFile = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envFile);

        file_put_contents($this->envPath(), $envFile);
    }

    public function writeFromEmptyEnv($key, $value)
    {
        $envFile = file_get_contents($this->envPath());

        $envFile .= "{$key}={$value}\n";

        file_put_contents($this->envPath(), $envFile);
    }
}
