<?php

namespace App\Actions\Leconfe;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class UpgradeAction
{
    use AsAction;

    public function handle(array $params)
    {
        $upgrader = new \App\Utils\Upgrader($params);
        $upgrader->run();
    }

    public function asCommand(Command $command): void
    {
        $installedVersion = app()->getInstalledVersion();
        $codeVersion = app()->getCodeVersion();

        warning('This action will run upgrade scripts your application. Please make sure you have a backup of your database and files before proceeding.');

        table(['Name', 'Version'], [
            ['Installed version', $installedVersion],
            ['Upgrade version', $codeVersion],
        ]);

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

            $upgrader = new \App\Utils\Upgrader(command: $command);
            $upgrader->run();

            info('Success upgrade Leconfe to ' . $codeVersion . '!');
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
