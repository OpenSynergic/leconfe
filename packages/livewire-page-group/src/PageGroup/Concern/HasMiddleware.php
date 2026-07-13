<?php

namespace Rahmanramsi\LivewirePageGroup\PageGroup\Concern;

use Livewire\Livewire;

trait HasMiddleware
{
    /**
     * @var array<string>
     */
    protected array $middleware = [];

    /**
     * @var array<string>
     */
    protected array $livewirePersistentMiddleware = [];

    /**
     * @param  array<string>  $middleware
     */
    public function middleware(array $middleware, bool $isPersistent = false): static
    {
        $this->middleware = [
            ...$this->middleware,
            ...$middleware,
        ];

        if ($isPersistent) {
            $this->persistentMiddleware($middleware);
        }

        return $this;
    }

    /**
     * @param  array<string>  $middleware
     */
    public function persistentMiddleware(array $middleware): static
    {
        $this->livewirePersistentMiddleware = [
            ...$this->livewirePersistentMiddleware,
            ...$middleware,
        ];

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMiddleware(): array
    {
        return [
            "pagegroup:{$this->getId()}",
            ...$this->middleware,
        ];
    }

    protected function registerLivewirePersistentMiddleware(): void
    {
        Livewire::addPersistentMiddleware($this->livewirePersistentMiddleware);

        $this->livewirePersistentMiddleware = [];
    }
}
