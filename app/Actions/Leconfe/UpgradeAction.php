<?php

namespace App\Actions\Leconfe;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\confirm;

class UpgradeAction
{
    use AsAction;

    public function handle(array $params)
    {
        $upgrade = new \App\Utils\Upgrader($params);
        $upgrade->run();
    }

    public function asCommand(Command $command): void
    {
        $confirmUpgrade = confirm('Are you sure you want to upgrade?');
        
        $data = [];

        $this->handle($data);

        $command->info('Done!');
    }

    public function getCommandSignature(): string
    {
        return 'leconfe:upgrade';
    }

    public function getCommandDescription(): string
    {
        return 'Upgrade leconfe application';
    }
}
