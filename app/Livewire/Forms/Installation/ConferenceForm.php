<?php

namespace App\Livewire\Forms\Installation;

use App\Actions\Conferences\ConferenceCreateAction;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ConferenceForm extends Form
{
    #[Rule('required')]
    public $name = null;

    #[Rule('required')]
    public $type = 'Offline';

    #[Rule('required')]
    public $current = true;

    public function process()
    {
        ConferenceCreateAction::run($this->all());
    }
}
