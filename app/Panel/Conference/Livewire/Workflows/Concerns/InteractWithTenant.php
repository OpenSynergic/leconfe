<?php

namespace App\Panel\Conference\Livewire\Workflows\Concerns;

use App\Models\Conference;

trait InteractWithTenant
{
    public Conference $conference;

    public function __construct()
    {
        $this->conference = app()->getCurrentConference();
    }
}
