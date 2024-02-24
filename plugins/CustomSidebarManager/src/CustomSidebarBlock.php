<?php

namespace CustomSidebarManager;

use App\Classes\Block;

class CustomSidebarBlock extends Block
{
    protected ?string $content;

    protected ?string $view = 'CustomSidebarManager::custom-sidebar';

    protected bool $showName;

    public function __construct(string $name, ?string $content, bool $showName = false)
    {
        $this->name = $name;
        $this->content = $content;
        $this->showName = $showName;
    }

    public function getViewData(): array
    {
        return [
            'id' => $this->getDatabaseName(),
            'name' => $this->name,
            'showName' => $this->showName,
            'content' => $this->content,
        ];
    }

    public function getSuffixName(): ?string
    {
        return '<span class="text-gray-500">(Custom Sidebar)</span>';
    }
}
