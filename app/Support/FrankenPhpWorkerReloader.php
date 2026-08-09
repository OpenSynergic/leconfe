<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Laravel\Octane\FrankenPhp\ServerStateFile;

class FrankenPhpWorkerReloader
{
    public function __construct(
        protected ServerStateFile $serverStateFile,
    ) {}

    public function reload(): void
    {
        $state = $this->serverStateFile->read()['state'];

        Http::post("http://{$state['adminHost']}:{$state['adminPort']}/frankenphp/workers/restart")
            ->throw();
    }
}
