<?php

namespace App\Actions\Permissions;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class PermissionUpdateAction
{
    use AsAction;

    public function handle(Permission $permission, array $data)
    {
        try {
            DB::beginTransaction();

            $permission->update($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $permission;
    }
}
