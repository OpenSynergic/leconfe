<?php

namespace App\Frontend\Conference\Pages;

use Livewire\Attributes\Rule;
use Filament\Facades\Filament;
use Livewire\Attributes\Title;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends Page
{
    use WithRateLimiting;

    protected static string $view = 'frontend.conference.pages.login';

    #[Rule('required|email')]
    public ?string $email = null;

    #[Rule('required')]
    public ?string $password = null;

    #[Rule('boolean')]
    public bool $remember = false;

    public function mount()
    {
        if (auth()->check()) {
            $this->redirect($this->getRedirectUrl(), navigate: false);
        }
    }

    public function getViewData() : array 
    {
        return [
            'registerUrl' => route('livewirePageGroup.conference.pages.register'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/') => 'Home',
            'Login',
        ];
    }

    public function getRedirectUrl(): string
    {
        return Filament::getPanel()->getUrl();
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

        if (! auth()->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        $this->redirect($this->getRedirectUrl(), navigate: false);
    }
}
