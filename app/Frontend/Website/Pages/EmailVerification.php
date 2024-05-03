<?php

namespace App\Frontend\Website\Pages;

use App\Facades\Settings;
use Livewire\Attributes\Title;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class EmailVerification extends Page
{
    use WithRateLimiting;

    protected static string $view = 'frontend.website.pages.email-verification';

    public function mount()
    {
        if (! Settings::get('must_verify_email')) {
            return redirect()->route('filament.panel.tenant');
        }

        if (! auth()->check()) {
            return redirect()->route('livewirePageGroup.website.pages.login');
        }

        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('filament.panel.tenant');
        }
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function sendEmailVerificationLink()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('filament.panel.tenant');
        }

        try {
            $this->rateLimit(1);
        } catch (TooManyRequestsException $exception) {
            $this->addError('email', __('email.verification.throttle', [
                'seconds' => $exception->secondsUntilAvailable,
            ]));

            return null;
        }

        auth()->user()->sendEmailVerificationNotification();

        session()->flash('success', true);
    }
}
