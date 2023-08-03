<?php

namespace App\Actions\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class UserCreateAction
{
    use AsAction;

    public function handle($data)
    {
        $user = User::create($data);

        if (isset($data['meta'])) {
            $user->setManyMeta($data['meta']);
        }

        return $user;
    }
}
