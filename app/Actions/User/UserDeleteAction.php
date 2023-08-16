<?php

namespace App\Actions\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class UserDeleteAction
{
    use AsAction;

    public function handle($data, User $user)
    {
        try {
            $user->submissions()->delete();
            $user->delete($data);
        } catch (\Throwable $th) {
            throw $th;
        }

        return $user;
    }
}
