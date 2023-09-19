<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate;

class PanelAuthenticate extends Authenticate
{
    protected function redirectTo($request): ?string
    {
        return route('livewirePageGroup.website.pages.login');
    }
}
