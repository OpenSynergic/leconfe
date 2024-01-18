<?php

namespace App\Actions\Leconfe;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\confirm;

class InstallAction
{
    use AsAction;

    public function handle(array $params)
    {
        $upgrade = new \App\Utils\Installer($params);
        $upgrade->run();
    }

    public function asCommand(Command $command): void
    {
        $data = [];

        $this->handle($data);

        $command->info('Application installed.');
    }

    public function getCommandSignature(): string
    {
        return 'leconfe:install';
    }

    public function getCommandDescription(): string
    {
        return 'Install leconfe application';
    }
}
