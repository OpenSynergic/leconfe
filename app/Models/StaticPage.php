<?php

namespace App\Models;

use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ContentType;
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
            ConferenceStatus::Active, ConferenceStatus::Archived, ConferenceStatus::Upcoming => route('livewirePageGroup.conference.pages.static-page', [
                'conference' => $this->conference->path,
                'staticPage' => $this->slug,
            ]),
            default => route('livewirePageGroup.website.pages.static-page', [
                'staticPage' => $this->slug,
            ]),
        };
    }
}
