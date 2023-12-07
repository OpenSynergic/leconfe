<?php

namespace App\Panel\Livewire\Workflows\Concerns;

use App\Models\Conference;
use Filament\Facades\Filament;

trait InteractWithTenant
{
    public Conference $conference;

    public function __construct()
    {
        $this->conference = app()->getCurrentConference();
    }
}
