<?php

namespace App\Managers;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class MetaTagManager
{
    protected array $metas = [];

    public function add(string $name, ?string $content): self
    {
        $this->metas[$name] = $content;

        return $this;
    }

    public function remove(string $name): self
    {
        unset($this->metas[$name]);

        return $this;
    }

    public function get(string $name): ?string
    {
        return $this->metas[$name] ?? null;
    }

    public function all(): Collection
    {
        return collect($this->metas);
    }

    public function render(): HtmlString
    {
        return new HtmlString(
            $this->all()
                ->map(fn ($content, $name) => "<meta name=\"{$name}\" content=\"{$content}\">")
                ->implode("\n")
        );
    }
}
