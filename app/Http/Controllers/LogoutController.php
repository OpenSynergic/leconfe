<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke()
    {
        auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        return app()->getCurrentConference() ? redirect()->route('livewirePageGroup.conference.pages.login') : redirect()->route('livewirePageGroup.website.pages.login');
    }
}
