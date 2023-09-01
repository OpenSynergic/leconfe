<?php

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RoleCreateAction
{
    use AsAction;

    public function handle(array $data) : Role
    {
        try {
            DB::beginTransaction();

            $role = Role::create($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $role;
    }
}
