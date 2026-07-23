<?php

namespace App\Infolists\Components\VerticalTabs;

use Exception;
use App\Facades\Hook;
use Closure;
use Illuminate\Support\Str;

class Tabs extends \Filament\Schemas\Components\Tabs
{
    protected string $view = 'infolists.components.vertical-tabs.tabs';

    protected bool|Closure $sticky = false;

    protected string|Closure $position = 'left';

    protected string|Closure $verticalSpace = '';

    public function position(string $position): static
    {
        $position = $this->evaluate($position);
        if ($position === 'left' || $position === 'right') {
            $this->position = $position;
        } else {
            throw new Exception('Invalid position provided. Only "left" and "right" are allowed.');
        }

        return $this;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function verticalSpace(string $verticalSpace): static
    {
        $verticalSpace = $this->evaluate($verticalSpace);
        if (str_starts_with($verticalSpace, 'space-y-')) {
            $this->verticalSpace = $verticalSpace;
        } else {
            throw new Exception('Invalid verticalSpace provided. Only "space-y-" are allowed.');
        }

        return $this;
    }

    public function getVerticalSpace(): string
    {
        return $this->verticalSpace;
    }

    public function sticky(bool|Closure $sticky = true): static
    {
        $this->sticky = $this->evaluate($sticky);

        return $this;
    }

    public function isSticky(): bool
    {
        return $this->sticky;
    }

    public function childComponents(array | \Filament\Schemas\Schema | \Filament\Schemas\Components\Component | \Filament\Actions\Action | \Filament\Actions\ActionGroup | string | \Illuminate\Contracts\Support\Htmlable | \Closure | null $components, string $key = 'default'): static
    {
        $id = Str::slug($this->label);

        Hook::call('VerticalTabs::Tabs::childComponents', [$id, &$components, $this]);

        $this->childComponents[$key] = $components;

        return $this;
    }
}
