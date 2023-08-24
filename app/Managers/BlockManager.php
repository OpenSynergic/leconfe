<?php

namespace App\Managers;

use App\Classes\Block;
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

    public function getBlocks(string $position): array
    {
        return collect(static::$blocks)
            ->map(function ($blockClass) {
                return app($blockClass);
            })
            ->reject(function (Block $block) use ($position) {
                return !$block->isActive() && $block->getPosition() != $position;
            })
            ->sort(function (Block $block) {
                return $block->getSort();
            })
            ->toArray();
    }

    public function getBlockSettings()
    {
        return static::$blockSettings;
    }
}
