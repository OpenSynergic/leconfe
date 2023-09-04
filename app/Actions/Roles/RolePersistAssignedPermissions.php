<?php

namespace App\Actions\Roles;

use App\Models\Enums\UserRole;
use App\Models\Role;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;

class RolePersistAssignedPermissions
{
    use AsAction;

    public string $commandSignature = 'role:persist-assigned-permissions';

    public function handle()
    {
        $data = [];

        foreach (UserRole::array() as $roleName) {
            $role = Role::query()
                ->with('permissions', fn ($query) => $query->orderBy('name', 'asc'))
                ->where('name', $roleName)
                ->first();

            $data[$roleName] = ($roleName !== UserRole::Admin) ? $role->permissions->pluck('name')->toArray() : Permission::query()->pluck('name')->toArray();
        }

        file_put_contents(storage_path('app/roleAssignedPermissions.json'), json_encode($data, JSON_PRETTY_PRINT));
    }

    public function asCommand(Command $command): void
    {
        try {
            $this->handle();

            $command->info('Success persist assigned permissions for roles');

        } catch (\Throwable $th) {
            $command->error($th->getMessage());
        }
    }
}
