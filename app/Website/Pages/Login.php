<?php

namespace App\Website\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Login extends Page
{
    use WithRateLimiting;

    protected static string $view = 'website.pages.login';
    
    #[Rule('required|email')]
    public null|string $email = null;

    #[Rule('required')]
    public null|string $password = null;

    #[Rule('boolean')]
    public bool $remember = false;

    public function mount()
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }
    }

    public function getBreadcrumbs() : array
    {
        return [
            url('/') => 'Home',
            'Login',
        ];
    }

    public function login() : ?LoginResponse
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
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
