<?php

namespace App\Actions\Leconfe;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

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
        warning('This action will run upgrade scripts your application. Please make sure you have a backup of your database and files before proceeding.');

        $confirmUpgrade = $command->option('confirm') ?: confirm('Are you sure you want to upgrade? This action cannot be undone. (y/n)');

        if (!$confirmUpgrade) {
            alert('Upgrade cancelled!');
            return;
        }


        try {
            info('Clearing cache...');

            $command->callSilently('optimize:clear');
            $command->callSilently('icons:clear');
            $command->callSilently('modelCache:clear');

            table(['Name', 'Version'], [
                ['Installed version', app()->getInstalledVersion()],
                ['Upgrade version', app()->getCodeVersion()],
            ]);


            $this->handle([]);

            info('Success upgrade Leconfe to ' . app()->getInstalledVersion() . '!');
        } catch (\Throwable $th) {
            $command->error($th->getMessage());
        }
    }

    public function getCommandSignature(): string
    {
        return 'leconfe:upgrade {--C|confirm}';
    }

    public function getCommandDescription(): string
    {
        return 'Upgrade leconfe application';
    }
}
