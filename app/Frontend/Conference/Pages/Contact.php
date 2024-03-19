<?php

namespace App\Frontend\Conference\Pages;

use Livewire\Attributes\Rule;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Contact extends Page
{
    protected static string $view = 'frontend.conference.pages.contact';

    #[Rule('required')]
    public ?string $name = null;

    #[Rule('required|email')]
    public ?string $email = null;

    #[Rule('required')]
    public ?string $message = null;

    public function submit()
    {
        $this->validate();
    }

    public function mount()
    {
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
