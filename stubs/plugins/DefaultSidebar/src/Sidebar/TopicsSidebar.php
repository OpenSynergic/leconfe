<?php

namespace DefaultSidebar\Sidebar;

use App\Classes\Sidebar;
use App\Models\Committee;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class TopicsSidebar extends Sidebar
{
    protected ?string $view = 'DefaultSidebar::sidebar.topics';

    public function getId(): string
    {
        return 'topics';
    }

    public function getName(): string
    {
        return 'Topics';
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
            'topics' => Topic::query()
                ->latest('created_at')
                ->limit(10)
                ->get(),
        ];
    }
}
