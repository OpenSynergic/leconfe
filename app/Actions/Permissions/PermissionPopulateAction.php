<?php

namespace App\Actions\Permissions;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Yaml\Yaml;

class PermissionPopulateAction
{
    use AsAction;

    public string $commandSignature = 'permission:populate';

    public function handle()
    {
        $file = base_path('data/permissions.yaml');
        if (! file_exists($file)) {
            throw new \Exception('File storage/app/permissions.yaml does not exist');
        }

        $permissions = Yaml::parseFile($file);

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }

    public function asCommand(Command $command): void
    {
        try {
            $this->handle();
            $command->info('Permissions populated from ./data/permissions.yaml');
        } catch (\Throwable $th) {
            $command->error($th->getMessage());
        }
    }
}
