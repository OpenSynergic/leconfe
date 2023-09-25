<?php

namespace App\Panel\Livewire\Workflows\Base;

use App\Panel\Livewire\Workflows\Concerns\CanModifySetting;
use App\Panel\Livewire\Workflows\Concerns\CanOpenStage;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Filament\Facades\Filament;
use Livewire\Component;

abstract class WorkflowStage extends Component
{
    use InteractWithTenant;
    use CanOpenStage;

    protected ?string $stage = null;

    protected ?string $stageLabel = null;

    public function getStageLabel(): ?string
    {
        return $this->stageLabel;
    }

    public function getStage(): ?string
    {
        return $this->stage;
    }
}
