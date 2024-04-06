<?php

namespace App\Frontend\Website\Pages;

use App\Frontend\Conference\Pages\Login as PagesLogin;

class Login extends PagesLogin
{
    public function getRedirectUrl(): string
    {
        return route('filament.administration.home');
    }


    public function getViewData() : array 
    {
        return [
            'registerUrl' => route('livewirePageGroup.website.pages.register'),
        ];
    }
}
