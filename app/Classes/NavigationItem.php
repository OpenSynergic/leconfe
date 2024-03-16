<?php

namespace App\Classes;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Closure;

class NavigationItem
{
    public function __construct(
        public string $label,
        public ?string $type,
        public ?array $data = [],
        public array $children = [],
    ) {
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    public function getUrl()
    {
        $currentConference = app()->getCurrentConference();

        $urlParser = $this->getUrlParserByType($this->type);

        return app()->call($urlParser, [
            'navItem' => $this,
            'conference' => $currentConference,
        ]);
    }

    protected function getUrlParserByType($type): Closure
    {
        return match ($type) {
            'external-link' => fn ($navItem) => $navItem->data['url'] ?? '#',
            'announcements' => fn (?Conference $conference = null) => route('livewirePageGroup.conference.pages.announcement-list', ['conference' => $conference->path]),
            'register' => fn () => route('livewirePageGroup.website.pages.register'),
            'login' => fn () => route('livewirePageGroup.website.pages.login'),
            'home' => fn (?Conference $conference = null) => $conference
                ? route('livewirePageGroup.conference.pages.home', ['conference' => $conference->path])
                : route('livewirePageGroup.website.pages.home'),
            'about' => fn (?Conference $conference = null) => route('livewirePageGroup.conference.pages.about', ['conference' => $conference->path]),
            'contact' => fn (?Conference $conference = null) => route('livewirePageGroup.conference.pages.contact', ['conference' => $conference->path]),
            'proceeding' => fn (?Conference $conference = null) => route('livewirePageGroup.conference.pages.proceeding', ['conference' => $conference->path]),
            default => fn () => '#',
        };
    }
}
