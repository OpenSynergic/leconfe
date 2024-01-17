<?php

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class RoleAssignDefaultPermissions
{
    use AsAction;

    public string $commandSignature = 'role:assign-default-permissions';

    public function handle()
    {
        $file = storage_path('app/roleAssignedPermissions.json');

        if (! file_exists($file)) {
            throw new \Exception("File $file  does not exist");
        }

        $roleAssignedPermissions = json_decode(file_get_contents($file));

        foreach ($roleAssignedPermissions as $roleName => $permissions) {
            if (empty($permissions)) {
                continue;
            }

            $role = Role::query()
                ->with(['permissions'])
                ->where('name', $roleName)
                ->first();

            $role->syncPermissions([...$permissions, ...$role->permissions->pluck('name')->toArray()]);
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
