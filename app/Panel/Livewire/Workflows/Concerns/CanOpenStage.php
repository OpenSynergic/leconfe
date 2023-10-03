<?php

namespace App\Panel\Livewire\Workflows\Concerns;

use App\Models\Conference;
use Livewire\Attributes\On;

trait CanOpenStage
{
    use CanModifySetting;
    public Conference $conference;

    protected bool $stageOpen = false;

    #[On('stage-status-changed')]
    public function isStageOpen(): bool
    {
        $this->stageOpen = $this->getSetting("open", false);
        return $this->stageOpen;
    }

    public function openStage(): void
    {
        $this->updateSetting("open", true);
        $this->updateSetting("start_date", now());
        $this->updateSetting("end_date", null);
        $this->dispatch('stage-status-changed'); // ->dispatch() from livewire
    }

    public function closeStage(): void
    {
        $this->updateSetting("open", false);
        $this->updateSetting("end_date", now());
        $this->dispatch('stage-status-changed'); // ->dispatch() from livewire
    }

    public function setSchedule(string $start, string $end): void
    {
        $this->updateSetting("start_date", $start);
        $this->updateSetting("end_date", $end);
    }
}
