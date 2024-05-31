<?php

namespace CustomSidebarManager;

use App\Classes\Plugin;
use App\Facades\SidebarFacade;
use CustomSidebarManager\CustomSidebarBlock;
use CustomSidebarManager\Pages\CustomSidebarManagerPage;
use Filament\Panel;

class CustomSidebarManagerPlugin extends Plugin
{
    public function boot()
    {
        $customBlocks = collect($this->getSetting('custom_sidebars', []))
            ->map(fn ($sidebar) => new CustomSidebarBlock($sidebar['id'], $sidebar['name'], $sidebar['content'], $sidebar['show_name'] ?? false));
    
        SidebarFacade::register($customBlocks->toArray());
    }

    public function onConferencePanel(Panel $panel): void
    {
        $panel->pages([
            CustomSidebarManagerPage::class,
        ]);
    }

    public function onAdministrationPanel(Panel $panel): void
    {
        $panel->pages([
            CustomSidebarManagerPage::class,
        ]);
    }

    public function getPluginPage(): ?string
    {
        try {
            return CustomSidebarManagerPage::getUrl();
        } catch (\Throwable $th) {
            return null;
        }

    }
};