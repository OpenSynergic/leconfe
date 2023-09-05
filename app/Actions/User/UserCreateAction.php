<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UserCreateAction
{
    use AsAction;

    public function handle($data)
    {
        try {
            DB::beginTransaction();

            $user = User::create($data);

            if (data_get($data, 'meta')) {
                $user->setManyMeta($data['meta']);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $user;
    }
}
