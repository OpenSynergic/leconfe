<?php

namespace DefaultSidebar\Sidebar;

use App\Classes\Sidebar;
use App\Models\Committee;
use App\Models\Enums\SerieState;
use App\Models\Serie;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class PreviousEventSidebar extends Sidebar
{
    protected ?string $view = 'DefaultSidebar::sidebar.previous-event';

    public function getId(): string
    {
        return 'previous-event';
    }

    public function getName(): string
    {
        return 'Previous Event';
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
            'previousEvents' => Serie::query()
                ->with('conference')
                ->where('id', '!=', app()->getCurrentSerieId())
                ->where('state', SerieState::Archived)
                ->orderBy('date_start', 'desc')
                ->take(3)
                ->get(),
        ];
    }
}
