<?php

namespace App\Frontend\Website\Pages;

use App\Frontend\Conference\Pages\Login as ConferenceLogin;

class Login extends ConferenceLogin
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
