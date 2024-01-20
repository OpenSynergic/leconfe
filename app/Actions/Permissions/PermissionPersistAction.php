<?php

namespace App\Actions\Permissions;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Yaml\Yaml;

class PermissionPersistAction
{
    use AsAction;

    public string $commandSignature = 'permission:persist';

    public function handle()
    {
        $permissions = Permission::query()
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->toArray();

        File::put(base_path('data'.DIRECTORY_SEPARATOR.'permissions.yaml'), Yaml::dump($permissions));
    }

    public function asCommand(Command $command): void
    {
        $this->handle();

        $command->info('Permissions persisted to storage/app/permissions.json');
    }
}
