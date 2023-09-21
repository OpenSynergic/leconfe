<?php

namespace App\Classes;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ConferenceType;
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
            'external-link' => fn($navItem) => $navItem->data['url'] ?? '#',
            'announcements' => fn (?Conference $conference = null) => match ($conference?->status) {
                ConferenceStatus::Archived => route('livewirePageGroup.archive-conference.pages.announcement-list', ['conference' => $conference->path]),
                default => route('livewirePageGroup.current-conference.pages.announcement-list')
            },
            'current-conference' => fn () => route('livewirePageGroup.current-conference.pages.home'),
            'register' => fn() => route('livewirePageGroup.website.pages.register'),
            'login' => fn() => route('livewirePageGroup.website.pages.login'),
            'home' => fn(?Conference $conference = null) => match($conference?->status){
                ConferenceStatus::Current => route('livewirePageGroup.current-conference.pages.home'),
                ConferenceStatus::Archived => route('livewirePageGroup.archive-conference.pages.home', ['conference' => $conference->path]),
                default => route('livewirePageGroup.website.pages.home'),
            },
            'about' => fn() => route('livewirePageGroup.current-conference.pages.about'),
            default => fn () => '#',
        };
    }
}
