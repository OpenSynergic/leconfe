<?php

namespace App\Classes;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

abstract class Sidebar implements Htmlable
{
    abstract public function getId(): string;
    
    abstract public function getName(): string;
    
    abstract public function render(): View;

    public function getPrefixName(): ?string
    {
        return null;
    }

    public function getSuffixName(): ?string
    {
        return null;
    }

    public function toHtml()
    {
        return $this->render()->render();
    }
}
