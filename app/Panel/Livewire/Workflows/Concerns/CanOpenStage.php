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
        if ($this->getSetting('start_date') >= now() && !$this->getSetting('end_date')) {
            $this->stageOpen = true;
        }

        if ($this->getSetting('end_date') >= now()) {
            $this->stageOpen = false;
        }

        return $this->stageOpen;
    }

    public function openStage(): void
    {
        $this->updateSetting("start_date", now());
        $this->updateSetting("end_date", null);
        $this->dispatch('stage-status-changed'); // ->dispatch() from livewire
    }

    public function closeStage(): void
    {
        $this->updateSetting("end_date", now());
        $this->dispatch('stage-status-changed'); // ->dispatch() from livewire
    }

    public function setSchedule(string $start, string $end): void
    {
        $this->updateSetting("start_date", $start);
        $this->updateSetting("end_date", $end);
    }
}
