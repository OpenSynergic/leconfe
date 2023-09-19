<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UserUpdateAction
{
    use AsAction;

    public function handle(User $user, array $data): User
    {
        try {
            DB::beginTransaction();

            $user->update($data);

            if (data_get($data, 'meta')) {
                $user->setManyMeta(data_get($data, 'meta'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $user;
    }
}
