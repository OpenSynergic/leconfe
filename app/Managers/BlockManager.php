<?php

namespace App\Managers;

use App\Classes\Block;
use Illuminate\Support\Collection;

class BlockManager
{
    protected array $blocks = [];

    public function boot(): void
    {
        if (! $this->blocks) {
            return;
        }

        foreach ($this->blocks as $block) {
            if (! $block instanceof Block) {
                throw new \Exception("{$block->getName()} must be an instance of ".Block::class);
            }
        }
    }

    public function registerBlocks(array $blocks): void
    {
        $this->blocks = array_merge($this->blocks, $blocks);
    }

    public function getBlocks(bool $activeOnly = true): Collection
    {
        return collect($this->blocks)
            ->reject(function (Block $block) use ($activeOnly) {
                if ($activeOnly) {
                    return !$this->isActiveBlock($block);
                }

                return false;
            })
            ->sortBy(function (Block $block){
                // Sort by the order of the block in the active block list if it exists.
                // start from 1 to avoid 0 index
                // if the block is not in the active block list, it will be placed at the end
                $index = array_search($block->getName(), $this->getActiveBlockList());
                
                return ($index === false) ? count($this->getActiveBlockList()) + 1 : $index + 1;
            })
            ->values();
    }

    public function isActiveBlock(Block $block): bool
    {
        return in_array($block->getName(), $this->getActiveBlockList());
    }

    public function getActiveBlockList(): array 
    {
        $context = app()->getCurrentConference() ?? app()->getSite();
        return $context->getMeta('sidebars') ?? [];
    }

    public function updateActiveBlockList(array $blocks): void
    {
        $context = app()->getCurrentConference() ?? app()->getSite();
        $context->setMeta('sidebars', $blocks);
    }
}
