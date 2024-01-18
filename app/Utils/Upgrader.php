<?php

namespace App\Utils;

use App\Actions\Permissions\PermissionPopulateAction;
use App\Actions\Roles\RoleAssignDefaultPermissions;
use App\Models\Version;
use App\Utils\Enums\UpgradeActionPriority;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class Upgrader
{
    public bool $isRunningInConsole;

    public array $actions = [];

    public function __construct(
        public array $params,
    ) {
        $this->isRunningInConsole = app()->runningInConsole();

        $this->prepareActions();
    }

    public function run()
    {
        $this->startActions();

        // $this->addNewApplicationVersion();
    }

    public function addNewApplicationVersion()
    {
        $version = Version::firstOrCreate([
            'product_name' => 'Leconfe',
            'product_folder' => 'leconfe',
            'version' => app()->getCodeVersion(),
        ], [
            'installed_at' => now(),
        ]);

        return $version;
    }

    public function startActions()
    {
        if (empty($this->actions)) {
            throw new \Exception('No upgrade script need to be run.');
        }

        // Sort by priority
        ksort($this->actions);

        foreach ($this->actions as $actions) {
            foreach ($actions as $key => $action) {
                if ($this->isRunningInConsole) {
                    spin(
                        fn () => $action(),
                        "Running upgrade script : $key"
                    );
                    continue;
                } 
                  
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

    public function readParam(string $name)
    {
        return $this->params[$name] ?? null;
    }
}
