<?php

namespace App\Utils;

use App\Actions\Permissions\PermissionPopulateAction;
use App\Actions\Roles\RoleAssignDefaultPermissions;
use App\Utils\Enums\UpgradeActionPriority;
use Closure;
use Illuminate\Support\Facades\Artisan;

class Upgrader
{
    public bool $isRunningInConsole;

    public array $actions = [];

    public function __construct(
        public array $params,
    ) {
        $this->isRunningInConsole = app()->runningInConsole();

        $this->prepareDefaultActions();
    }

    public function run()
    {
        $this->startActions();
    }

    public function startActions()
    {
        // Sort by priority
        ksort($this->actions);
    
        foreach ($this->actions as $actions) {
            foreach ($actions as $action) {
                $action();
            }
        }
    }

    public function prepareDefaultActions()
    {
        $this->addAction('migrate', fn() => Artisan::call('migrate --force'), UpgradeActionPriority::HIGH);
        $this->addAction(
            'update-permissions-and-role', function(){
            PermissionPopulateAction::run();
            RoleAssignDefaultPermissions::run();
        });
    }

    public function prepareActionsByApplicationVersion()
    {

    }

    public function addAction(string $name, Closure $action, UpgradeActionPriority $priority = UpgradeActionPriority::MEDIUM)
    {
        $this->actions[$priority->value][$name] = $action;
    }

    public function readParam(string $name)
    {
        return $this->params[$name] ?? null;
    }
}
