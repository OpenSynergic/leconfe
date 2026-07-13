<?php

namespace Rahmanramsi\LivewirePageGroup\PageGroup\Concern;

trait HasLayout
{
    protected ?string $layout = null;

    public function layout(string $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }
}
