<?php

namespace Rahmanramsi\LivewirePageGroup\PageGroup\Concern;

use Closure;
use Laravel\SerializableClosure\Serializers\Native;

trait HasRoutes
{
    protected Closure|Native|null $routes = null;

    protected ?string $domain = null;

    protected string $path = '';

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function domain(?string $domain = null): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function routes(?Closure $routes): static
    {
        $this->routes = $routes;

        return $this;
    }

    public function getRoutes(): ?Closure
    {
        return $this->routes;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
