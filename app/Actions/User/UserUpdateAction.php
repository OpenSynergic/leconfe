<?php

namespace App\Actions\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class UserUpdateAction
{
    use AsAction;

    public function handle(array $data, User $user)
    {
        $user->update($data);

        if (isset($data['meta'])) {
            $user->setManyMeta($data['meta']);
        }

        return $user;
    }
}
