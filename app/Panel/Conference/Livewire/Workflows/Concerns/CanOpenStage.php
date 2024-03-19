<?php

namespace App\Panel\Conference\Livewire\Workflows\Concerns;

use App\Models\Conference;
use Carbon\Carbon;
use Livewire\Attributes\On;

trait CanOpenStage
{
    use CanModifySetting;

    public Conference $conference;

    protected bool $stageOpen = false;

    #[On('stage-status-changed')]
    public function isStageOpen(): bool
    {
        $currentTime = Carbon::now();
        $startDate = Carbon::parse($this->getSetting('start_date'));
        $endDate = Carbon::parse($this->getSetting('end_date'));

        if ($currentTime >= $startDate && $currentTime < $endDate) {
            $this->stageOpen = true;
        }

        return $this->stageOpen;
    }

    public function openStage(): void
    {
        $this->updateSetting('start_date', now());
        $this->updateSetting('end_date', null);
        $this->dispatch('stage-status-changed'); // ->dispatch() from livewire
    }

    public function closeStage(): void
    {
        $this->updateSetting('end_date', now());
        $this->dispatch('stage-status-changed'); // ->dispatch() from livewire
    }

    public function setSchedule(string $start, string $end): void
    {
        $this->updateSetting('start_date', $start);
        $this->updateSetting('end_date', $end);
        $this->dispatch('stage-status-changed');
    }
}
