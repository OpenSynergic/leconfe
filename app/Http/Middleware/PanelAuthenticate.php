<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate;

class PanelAuthenticate extends Authenticate
{
    protected function redirectTo($request): ?string
    {
        return app()->getCurrentConferenceId() ? route('livewirePageGroup.conference.pages.login') : route('livewirePageGroup.website.pages.login');
    }
}
