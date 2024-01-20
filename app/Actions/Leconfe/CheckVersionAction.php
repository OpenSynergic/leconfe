<?php

namespace App\Actions\Leconfe;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckVersionAction
{
    use AsAction;

    public function handle()
    {
        return app()->getInstalledVersion();
    }

    public function asCommand(Command $command): void
    {
        $command->info('Leconfe version: ' . $this->handle());
    }

    public function getCommandSignature(): string
    {
        return 'leconfe:check';
    }

    public function getCommandDescription(): string
    {
        return 'Check leconfe version';
    }
}
