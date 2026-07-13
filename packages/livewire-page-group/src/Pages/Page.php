<?php

namespace Rahmanramsi\LivewirePageGroup\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;

abstract class Page extends Component
{
    use Concern\HasRoutes;

    protected static ?string $title = null;

    protected static string $layout = 'livewire-page-group::components.layouts.app';

    protected static string $view;

    protected static bool $isDiscovered = true;

    public function render(): View
    {
        return view(static::$view, $this->getViewData())
            ->layout(static::getLayout(), [
                'livewire' => $this,
                ...$this->getLayoutData(),
            ])
            ->title($this->getTitle());
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? (string) str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }

    public static function getLayout(): string
    {
        $pageGroup = LivewirePageGroup::getCurrentPageGroup();

        return $pageGroup?->getLayout() ?? static::$layout;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getLayoutData(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [];
    }
}
