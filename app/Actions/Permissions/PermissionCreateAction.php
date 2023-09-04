<?php

namespace App\Actions\Permissions;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class PermissionCreateAction
{
    use AsAction;

    public function handle(array $data): Permission
    {
        try {
            DB::beginTransaction();

            $permission = Permission::create($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $permission;
    }
}
