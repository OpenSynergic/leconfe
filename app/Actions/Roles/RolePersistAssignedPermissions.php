<?php

namespace App\Actions\Roles;

use App\Models\Enums\UserRole;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Yaml\Yaml;

class RolePersistAssignedPermissions
{
    use AsAction;

    public function handle(Role $role)
    {
        $file = base_path('data' . DIRECTORY_SEPARATOR . 'roleAssignedPermissions.yaml');

        if (!file_exists($file)) {
            File::put($file, '');
        }

        $roleAssignedPermissions = Yaml::parseFile($file) ?? [];
        $roleAssignedPermissions[$role->name] = ($role->name !== UserRole::Admin) ? $role->permissions->pluck('name')->toArray() : Permission::query()->pluck('name')->toArray();


        File::put($file, Yaml::dump($roleAssignedPermissions));
    }
}
