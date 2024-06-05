<?php

namespace DefaultSidebar\Sidebar;

use App\Classes\Sidebar;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class SubmitNowSidebar extends Sidebar
{
    protected ?string $view = 'DefaultSidebar::sidebar.submit-now';

    public function getId(): string
    {
        return 'submit-now';
    }

    public function getName(): string
    {
        return 'Submit Now';
    }

    public function render(): View
    {
        return view($this->view, $this->getViewData());
    }

    public function getViewData(): array
    {
        return [
            'id' => $this->getId(),
            'submissionUrl' => route('filament.conference.resources.submissions.index')
        ];
    }
}
