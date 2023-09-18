<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;

class RegisterAccount extends Form
{
    #[Rule('required')]
    public $given_name = null;

    #[Rule('required')]
    public $family_name = null;
    
    public $affiliation = null;

    public $country = null;

    #[Rule('required|email')]
    public $email = null;

    #[Rule('required|confirmed')]
    public $password = null;

    #[Rule('required')]
    public $password_confirmation = null;

    public function save() 
    {
        dd($this->validate());

        dd($this->all());
    }
}
