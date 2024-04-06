<?php

namespace App\Actions\Roles;

use App\Models\Conference;
use App\Models\Role;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Yaml\Yaml;

class RoleAssignDefaultPermissions
{
    use AsAction;

    public string $commandSignature = 'role:assign-default-permissions';

    public function handle(Role $role)
    {
        $file = base_path('data/roleAssignedPermissions.yaml');

        if (! file_exists($file)) {
            throw new \Exception("File $file  does not exist");
        }

        $roleAssignedPermissions = Yaml::parseFile($file);
    
        if(!array_key_exists($role->name, $roleAssignedPermissions)) return;
        
        $role?->syncPermissions([...$roleAssignedPermissions[$role->name], ...$role->permissions->pluck('name')->toArray()]);
    }
}
