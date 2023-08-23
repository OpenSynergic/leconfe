<?php

namespace App\Livewire\Forms\Installation;

use Livewire\Attributes\Rule;
use Livewire\Form;

class ConferenceForm extends Form
{
    #[Rule('required')]
    public $name = null;

    #[Rule('required')]
    public $type = 'Offline';
}
