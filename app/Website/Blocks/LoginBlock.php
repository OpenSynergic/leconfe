<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;

class LoginBlock extends Block
{
    use WithRateLimiting;

    protected ?string $view = 'website.blocks.login-block';

    protected ?int $sort = 2;

    protected string $name = 'Login Block';

    protected ?string $position = 'right';

    #[Rule('required|email')]
    public ?string $email = null;

    #[Rule('required')]
    public ?string $password = null;

    public function login()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->addError('email', __('auth.throttle', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => ceil($exception->secondsUntilAvailable / 60),
            ]));

            return null;
        }
        $this->validate();

        if (! Filament::auth()->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        $this->redirect(Filament::getUrl(), navigate: false);
    }
}
