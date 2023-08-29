<?php

namespace App\Website\Pages;

use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Register extends Page
{
    protected static string $view = 'website.pages.register';

    public function mount()
    {
        //
    }
    
    public function getBreadcrumbs() : array
    {
        return [];
    }
}
