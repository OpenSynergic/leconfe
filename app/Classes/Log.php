<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;

class Log
{
    public array $properties = [];

    public ?Model $causer = null;

    use Conditionable;

    public function __construct(
        public Model $subject,
        public string $name,
        public ?string $description = null
    ) {
    }

    public static function make(
        Model $subject,
        string $name,
        ?string $description = null
    ): self {
        return app(static::class, [
            'subject' => $subject,
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function subject(Model $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function causer(?Model $causer): static
    {
        $this->causer = $causer;

        return $this;
    }

    public function by(?Model $causer): static
    {
        return $this->causer($causer);
    }

    public function byAnonymous(): static
    {
        return $this->causer(null);
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function properties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    public function save(): void
    {
        activity($this->name)
            ->when(
                $this->causer,
                fn ($log) => $log->by($this->causer),
                fn ($log) => $log->byAnonymous()
            )
            ->when(
                $this->properties,
                fn ($log) => $log->withProperties($this->properties)
            )
            ->performedOn($this->subject)
            ->log($this->description);
    }
}
