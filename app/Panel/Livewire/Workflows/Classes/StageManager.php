<?php

namespace App\Panel\Livewire\Workflows\Classes;

use App\Panel\Livewire\Workflows\Concerns\CanOpenStage;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;

class StageManager
{
    use CanOpenStage;
    use InteractWithTenant {
        InteractWithTenant::__construct as __constructInteractWithTenant;
    }

    public function __construct(protected string $stage)
    {
        $this->__constructInteractWithTenant();
    }

    public static function callForAbstract(): static
    {
        return static::stage('call-for-abstract');
    }

    public static function payment(): static
    {
        return static::stage('payment');
    }

    public static function peerReview(): static
    {
        return static::stage('peer-review');
    }

    public static function editing(): static
    {
        return static::stage('editing');
    }

    public static function stage(string $stage): static
    {
        return app(static::class, ['stage' => $stage]);
    }
}
