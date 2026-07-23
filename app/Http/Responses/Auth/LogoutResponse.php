<?php

namespace App\Http\Responses\Auth;

use Illuminate\Http\RedirectResponse;

class LogoutResponse implements \Filament\Auth\Http\Responses\Contracts\LogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        if (app()->getCurrentScheduledConference()) {
            return redirect()->route('livewirePageGroup.scheduledConference.pages.login');
        }

        if (app()->getCurrentConference()) {
            return redirect()->route('livewirePageGroup.conference.pages.login');
        }

        return redirect()->route('livewirePageGroup.website.pages.login');
    }
}
