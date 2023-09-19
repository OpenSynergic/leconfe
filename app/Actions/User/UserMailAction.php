<?php

namespace App\Actions\User;

use App\Mail\MailUser;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class UserMailAction
{
    use AsAction;

    public function handle(User $user, string $subject, string $message): void
    {
        Mail::to($user)->send(new MailUser($subject, $message));
    }
}
