<?php

namespace App\Actions\Leconfe;

use Throwable;
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
        try {
            $command->info('Leconfe version: '.$this->handle());
        } catch (Throwable $th) {
            $command->error($th->getMessage());
        }

    }

    public function getCommandSignature(): string
    {
        return 'leconfe:check-version';
    }

    public function getCommandDescription(): string
    {
        return 'Check leconfe version';
    }
}
