<?php

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Yaml\Yaml;

class RoleAssignDefaultPermissions
{
    use AsAction;

    public string $commandSignature = 'role:assign-default-permissions';

    public function handle()
    {
        $file = base_path('data/roleAssignedPermissions.yaml');

        if (! file_exists($file)) {
            throw new \Exception("File $file  does not exist");
        }

        $roleAssignedPermissions = Yaml::parseFile($file);

        foreach ($roleAssignedPermissions as $roleName => $permissions) {
            if (empty($permissions)) {
                continue;
            }

            $role = Role::query()
                ->with(['permissions'])
                ->where('name', $roleName)
                ->first();

            $role?->syncPermissions([...$permissions, ...$role->permissions->pluck('name')->toArray()]);
        }
    }

    public function asCommand(Command $command): void
    {
        try {
            $this->handle();

            $command->info('Success assign default permission for roles');

        } catch (\Throwable $th) {
            $command->error($th->getMessage());
        }
    }
}
