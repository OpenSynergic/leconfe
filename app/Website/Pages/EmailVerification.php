<?php

namespace App\Website\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class EmailVerification extends Page
{
    use WithRateLimiting;

    protected static string $view = 'website.pages.email-verification';

    public function mount()
    {
        if(!setting('must_verify_email')){
            return redirect()->route('filament.panel.tenant');
        }

        if(!auth()->check()){
            return redirect()->route('livewirePageGroup.website.pages.login');
        }

        if(auth()->user()->hasVerifiedEmail()){
            return redirect()->route('filament.panel.tenant');
        }
    }
    
    public function getBreadcrumbs() : array
    {
        return [];
    }


    public function sendEmailVerificationLink()
    {
        if(auth()->user()->hasVerifiedEmail()){
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
