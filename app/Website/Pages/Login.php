<?php

namespace App\Website\Pages;

use App\Conference\Pages\Login as PagesLogin;

class Login extends PagesLogin
{
    public function getRedirectUrl(): string
    {
        return route("livewirePageGroup.website.pages.home");
    }
}
