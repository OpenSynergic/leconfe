<?php

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RoleUpdateAction
{
    use AsAction;

    public function handle(Role $role, array $data)
    {
        try {
            DB::beginTransaction();

            $role->update($data);

            if(isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $role;
    }
}
