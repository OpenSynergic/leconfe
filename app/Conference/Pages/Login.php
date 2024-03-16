<?php

namespace App\Conference\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Login extends Page
{
    use WithRateLimiting;

    protected static string $view = 'conference.pages.login';

    #[Rule('required|email')]
    public ?string $email = null;

    #[Rule('required')]
    public ?string $password = null;

    #[Rule('boolean')]
    public bool $remember = false;

    public function mount()
    {
        if (Filament::auth()->check()) {
            $this->redirect(Filament::getUrl(), navigate: false);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/') => 'Home',
            'Login',
        ];
    }

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

        if (!Filament::auth()->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        $this->redirect(Filament::getUrl(), navigate: false);
    }
}
