<?php

namespace App\Actions\Permissions;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;

class PermissionPopulateAction
{
    use AsAction;

    public string $commandSignature = 'permission:populate';

    public function handle()
    {
        // Read permissions from storage/app/permissions.json
        if (! file_exists(storage_path('app/permissions.json'))) {
            throw new \Exception('File storage/app/permissions.json does not exist');
        }

        $permissions = json_decode(file_get_contents(storage_path('app/permissions.json')));

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }

    public function asCommand(Command $command): void
    {
        try {
            $this->handle();
            $command->info('Permissions populated from storage/app/permissions.json');
        } catch (\Throwable $th) {
            $command->error($th->getMessage());
        }
    }
}
