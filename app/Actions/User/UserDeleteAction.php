<?php

namespace App\Actions\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class UserDeleteAction
{
    use AsAction;

    public function handle(array $data, User $user)
    {
        if ($data['options'] === 'delete' || is_null($data['options'])) {
            $user->submissions()->delete();
            $user->delete();
        }

        return dd('User data is stored temporarily for next action');
    }
}
