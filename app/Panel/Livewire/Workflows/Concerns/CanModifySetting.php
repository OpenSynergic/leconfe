<?php

namespace App\Panel\Livewire\Workflows\Concerns;

trait CanModifySetting
{
    public array $settings = [];

    public function getSetting(string $key, mixed $default = false): mixed
    {
        return $this->conference->getMeta("workflow.{$this->stage}.{$key}", $default);
    }

    public function updateSetting(string $key, mixed $value): void
    {
        $this->conference->setMeta("workflow.{$this->stage}.{$key}", $value);
    }
}
