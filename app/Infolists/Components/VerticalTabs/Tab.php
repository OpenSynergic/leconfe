<?php

namespace App\Infolists\Components\VerticalTabs;

use App\Facades\Hook;
use Closure;

class Tab extends \Filament\Schemas\Components\Tabs\Tab
{
    protected string $view = 'infolists.components.vertical-tabs.tab';

    public function childComponents(array | \Filament\Schemas\Schema | \Filament\Schemas\Components\Component | \Filament\Actions\Action | \Filament\Actions\ActionGroup | string | \Illuminate\Contracts\Support\Htmlable | \Closure | null $components, string $key = 'default'): static
    {
        $id = $this->id;

        Hook::call('VerticalTabs::Tab::childComponents', [$id, &$components, $this]);

        $this->childComponents[$key] = $components;

        return $this;
    }
}
