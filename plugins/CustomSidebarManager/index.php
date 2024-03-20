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
            ->map(fn ($block) => new CustomSidebarBlock($block['name'], $block['content'], $block['show_name'] ?? false));

        Block::registerBlocks($customBlocks->toArray());
    }

    public function onConferencePanel(Panel $panel): void
    {
        $panel->pages([
            CustomSidebarManagerPage::class,
        ]);
    }

    public function getPluginPage(): ?string
    {
        $path = app()->getCurrentConference()->path;

        return url("panel/{$path}/custom-sidebar-manager-page");
    }
};
