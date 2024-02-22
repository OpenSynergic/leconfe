<?php

use App\Classes\Plugin;
use App\Facades\Block;
use CustomSidebarManager\CustomSidebarBlock;
use CustomSidebarManager\Pages\CustomSidebarManagerPage;
use Filament\Panel;

require 'vendor/autoload.php';

return new class extends Plugin
{
    public function boot()
    {
        $customBlocks = collect($this->getSetting('blocks', []))
            ->map(fn ($block) => new CustomSidebarBlock($block['name'], $block['content']));

        Block::registerBlocks($customBlocks->toArray());
    }

    public function onPanel(Panel $panel): void
    {
        $panel->pages([
            CustomSidebarManagerPage::class,
        ]);
    }

    public function getPluginPage(): ?string
    {
        return url('/panel/'.app()->getCurrentConference()->path.'/custom-sidebar-manager-page');
    }
};
