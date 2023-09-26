<?php

namespace App\Models;

use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ContentType;
use App\Models\Scopes\StaticPageScope;
use Illuminate\Database\Eloquent\Builder;

class StaticPage extends UserContent
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('static_page', function (Builder $builder) {
            $builder->where('content_type', ContentType::StaticPage->value);
        });
    }


    public function getUrl(): string
    {
        return match ($this->conference?->status) {
            ConferenceStatus::Active => route('livewirePageGroup.current-conference.pages.static-page', [
                'staticPage' => $this->slug,
            ]),
            ConferenceStatus::Archived => route('livewirePageGroup.archive-conference.pages.static-page', [
                'staticPage' => $this->slug,
                'conference' => $this->conference->path,
            ]),
            ConferenceStatus::Upcoming => '#', // Currently, upcoming conferences are not accessible
            default => route('livewirePageGroup.website.pages.static-page', [
                'staticPage' => $this->slug,
            ]),
        };
    }
}
