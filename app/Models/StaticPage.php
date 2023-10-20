<?php

namespace App\Models;

use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ContentType;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class StaticPage extends UserContent implements Sitemapable
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('static_page', function (Builder $builder) {
            $builder->where('content_type', ContentType::StaticPage->value);
        });
    }

    public function toSitemapTag(): Url|string|array
    {
        return $this->getUrl();
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
