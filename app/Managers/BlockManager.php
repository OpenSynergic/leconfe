<?php

namespace App\Managers;

use App\Classes\Block;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;

class BlockManager
{
    protected static array $blocks = [];

    protected static $blockSettings;

    public static function boot(): void
    {
        if (!static::$blocks) {
            return;
        }

        foreach (static::$blocks as $blockClass) {
            if (is_subclass_of($blockClass, \Livewire\Component::class)) {
                $componentName = app(ComponentRegistry::class)->getName($blockClass);
                Livewire::component($componentName, $blockClass);
            }
        }
    }

    public function registerBlocks(array $blocks): void
    {
        static::$blocks = array_merge(static::$blocks, $blocks);
    }

    public function getBlocks(string $position, bool $includeInactive = false): Collection
    {
        return collect(static::$blocks)
            ->map(fn ($block) => app($block))
            ->reject(function (Block $block) use ($position, $includeInactive) {
                if ($includeInactive) {
                    return $block->getPosition() != $position;
                }
                return !$block->isActive() || $block->getPosition() != $position;
            })
            ->sortBy(fn (Block $block) => $block->getSort());
    }

    public function getBlockSettings(): array
    {
        return static::$blockSettings;
    }
}
