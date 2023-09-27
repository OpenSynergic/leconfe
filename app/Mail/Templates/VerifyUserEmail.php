<?php

namespace App\Mail\Templates;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyUserEmail extends TemplateMailable
{
    use Queueable, SerializesModels;

    public string $userFullName;

    public string $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        // parent::__construct();

        $this->userFullName = $user->full_name;
        $this->verificationUrl = $this->verificationUrl($user);

    }

    protected function verificationUrl($user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }
}
