<?php

namespace DefaultSidebar\Sidebar;

use App\Classes\Sidebar;
use App\Models\Committee;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class CommitteeSidebar extends Sidebar
{
    protected ?string $view = 'DefaultSidebar::sidebar.committee';

    public function getId(): string
    {
        return 'committee';
    }

    public function getName(): string
    {
        return 'Committee';
    }

    public function render(): View
    {
        return view($this->view, $this->getViewData());
    }

    public function getViewData(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'committees' => Committee::query()->orderBy('order_column')->take(3)->get(),
        ];
    }
}
