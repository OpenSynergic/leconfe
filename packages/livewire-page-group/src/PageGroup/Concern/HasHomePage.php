<?php

namespace Rahmanramsi\LivewirePageGroup\PageGroup\Concern;

use Rahmanramsi\LivewirePageGroup\Pages\HomePage;

trait HasHomePage
{
    protected string $homePage = HomePage::class;

    public function homePage(string $homePage): static
    {
        $this->homePage = $homePage;

        return $this;
    }

    public function getHomePage(): string
    {
        return $this->homePage;
    }
}
