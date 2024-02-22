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

        foreach ($this->blocks as $block) {
            if (!$block instanceof Block) {
                throw new \Exception("{$block->getName()} must be an instance of ".Block::class);
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
            ->reject(function (Block $block) use ($position, $includeInactive) {
                if ($includeInactive) {
                    return $block->getPosition() != $position;
                }

                return ! $block->isActive() || $block->getPosition() != $position;
            })
            ->sortBy(fn (Block $block) => $block->getSort());
    }
}
