<?php

namespace App\Panel\Livewire\Workflows\Base;

use App\Panel\Livewire\Workflows\Concerns\CanOpenStage;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Livewire\Component;

abstract class WorkflowStage extends Component
{
    use CanOpenStage, InteractWithTenant;

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
