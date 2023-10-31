<?php

namespace App\Panel\Livewire\Workflows\Classes;

use App\Panel\Livewire\Workflows\Concerns\CanOpenStage;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;

class StageManager
{
    use InteractWithTenant {
        InteractWithTenant::__construct as __constructInteractWithTenant;
    }

    use CanOpenStage;

    public function __construct(protected string $stage)
    {
        $this->__constructInteractWithTenant();
    }

    public static function stage(string $stage): static
    {
        return app(static::class, ['stage' => $stage]);
    }
}
