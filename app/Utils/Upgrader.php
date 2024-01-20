<?php

namespace App\Utils;

use App\Exceptions\Upgrade\NoUpgradeScript;
use App\Utils\Enums\UpgradeActionPriority;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class Upgrader extends Installer
{
    public array $actions = [];

    public string $installedVersion;
    public string $codeVersion;

    public function __construct(
        public array $params = [],
        public ?Command $command = null,
    ) {
        $this->installedVersion = App::getInstalledVersion();
        $this->codeVersion = App::getCodeVersion();
    }

    public function run()
    {
        try {
            // count time to upgrade
            $start = microtime(true);

            $this->prepareActions();

            $this->startActions();

            $this->addNewApplicationVersion();

            $this->optimize();
        } catch (\Throwable $th) {
            if (!$th instanceof NoUpgradeScript) {
                activity('leconfe')
                    ->causedByAnonymous()
                    ->event('upgrade')
                    ->withProperties([
                        'from' => $this->installedVersion,
                        'to' => $this->codeVersion,
                        'duration' => round(microtime(true) - $start, 2),
                        'status' => 'failed',
                    ])
                    ->log($th->getMessage());
            }

            throw $th;
            return;
        }

        activity('leconfe')
            ->causedByAnonymous()
            ->event('upgrade')
            ->withProperties([
                'from' => $this->installedVersion,
                'to' => $this->codeVersion,
                'duration' => round(microtime(true) - $start, 2),
                'status' => 'success',
            ])
            ->log('Upgrade success');
    }

    public function log($message, $properties = [])
    {
        activity('leconfe')
            ->causedByAnonymous()
            ->event('upgrade')
            ->withProperties([
                'from' => $this->installedVersion,
                'to' => $this->codeVersion,
                ...$properties,
            ])
            ->log($message);
    }

    public function addNewApplicationVersion()
    {
        $version = app()->getVersion();
        $version->save();

        return $version;
    }

    public function startActions()
    {
        if (empty($this->actions)) {
            throw new NoUpgradeScript($this->installedVersion, $this->codeVersion);
        }

        // Sort by priority
        ksort($this->actions);

        foreach ($this->actions as $actions) {
            foreach ($actions as $key => $action) {
                $this->command?->info("Running upgrade actions : $key");
                $action();
            }
        }
    }

    public function prepareActions()
    {
        $installedVersion = App::getInstalledVersion();
        $applicationVersion = App::getCodeVersion();
        $schemas = UpgradeSchema::getSchemasByVersion($installedVersion, $applicationVersion);
        foreach ($schemas as $key => $value) {
            $this->addAction($key, $value);
        }
    }

    public function addAction(string $name, callable $action, UpgradeActionPriority $priority = UpgradeActionPriority::MEDIUM)
    {
        $this->actions[$priority->value][$name] = $action;
    }
}
