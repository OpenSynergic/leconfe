<?php

namespace App\Livewire\Website;

use App\Models\Conference;
use Livewire\Component;

class TopBarNavigation extends Component
{
    public Conference $conference;

    public function render()
    {
        return view('livewire.website.top-bar-navigation');
    }
}
