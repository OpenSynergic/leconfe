<?php

namespace App\Managers;

use App\Livewire\Block;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;

class BlockManager
{
    protected array $blocks = [];

    public function boot(): void
    {
        if (! $this->blocks) {
            return;
        }

        foreach ($this->blocks as $blockClass) {
            if (is_subclass_of($blockClass, Block::class)) {
                $componentName = app(ComponentRegistry::class)->getName($blockClass);
                Livewire::component($componentName, $blockClass);
            } else {
                throw new \Exception("{$blockClass} must be an instance of ".Block::class);
            }
        }
    }

    public function registerBlocks(array $blocks): void
    {
        $this->blocks = array_merge($this->blocks, $blocks);
    }

    public function getBlocks(string $position, bool $includeInactive = false): Collection
    {
        return collect($this->blocks)
            ->map(fn ($block) => app($block))
            ->reject(function (Block $block) use ($position, $includeInactive) {
                if ($includeInactive) {
                    return $block->getPosition() != $position;
                }

                return ! $block->isActive() || $block->getPosition() != $position;
            })
            ->sortBy(fn (Block $block) => $block->getSort());
    }
}
