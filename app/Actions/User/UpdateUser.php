<?php

namespace App\Actions\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUser
{
    use AsAction;

    public function handle(array $data, User $user)
    {
        $user->update($data);

        $user->setManyMeta($data['meta']);

        return $user;
    }
}
