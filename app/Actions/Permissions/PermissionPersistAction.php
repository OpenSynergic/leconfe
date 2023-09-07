<?php

namespace App\Actions\Permissions;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;

class PermissionPersistAction
{
    use AsAction;

    public string $commandSignature = 'permission:persist';

    public function handle()
    {
        // save permissions to file in storage/app/permissions.json
        $permissions = Permission::query()
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->toArray();

        file_put_contents(storage_path('app/permissions.json'), json_encode($permissions, JSON_PRETTY_PRINT));
    }

    public function asCommand(Command $command): void
    {
        $this->handle();

        $command->info('Permissions persisted to storage/app/permissions.json');
    }
}
