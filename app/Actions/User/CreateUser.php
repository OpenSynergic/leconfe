<?php

namespace App\Actions\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateUser
{
    use AsAction;

    public function handle($data)
    {
        $user = User::create($data);

        $user->setManyMeta($data['meta']);

        return $user;
    }
}
