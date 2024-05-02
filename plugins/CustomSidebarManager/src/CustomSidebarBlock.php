<?php

namespace CustomSidebarManager;

use App\Classes\Sidebar;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class CustomSidebarBlock extends Sidebar
{
    protected ?string $view = 'CustomSidebarManager::custom-sidebar';

    public function __construct(
        public string $id,
        public string $name,
        public string $content,
        public bool $showName = false, 
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function render(): View
    {
        return view($this->view, $this->getViewData());
    }

    public function getViewData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => $this->content,
            'showName' => $this->showName,
        ];
    }

    public function getSuffixName(): ?string
    {
        return new HtmlString('<span class="text-gray-500">(Custom Sidebar)</span>');
    }
}
