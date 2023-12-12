<?php

namespace App\Models;

use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ContentType;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class Announcement extends UserContent implements Sitemapable
{
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Announcement $announcement) {
            $announcement->content_type = ContentType::Announcement->value;
            $announcement->created_by = auth()?->id();
        });

        static::addGlobalScope('announcement', function (Builder $builder) {
            $builder->where('content_type', ContentType::Announcement->value);
        });
    }

    public function toSitemapTag(): Url|string|array
    {
        return $this->getUrl();
    }

    public function getUrl()
    {
        return match ($this->conference->status) {
            ConferenceStatus::Active => route('livewirePageGroup.current-conference.pages.announcement-page', [
                'announcement' => $this->id,
            ]),
            ConferenceStatus::Archived => route('livewirePageGroup.archive-conference.pages.announcement-page', [
                'conference' => $this->conference->id,
                'announcement' => $this->id,
            ]),
            ConferenceStatus::Upcoming => '#', // Currently, upcoming conferences are not accessible
            default => '#',
        };
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('small')
            ->keepOriginalImageFormat()
            ->width(200);

        $this->addMediaConversion('thumb')
            ->keepOriginalImageFormat()
            ->width(400);

        $this->addMediaConversion('thumb-xl')
            ->keepOriginalImageFormat()
            ->width(600);
    }
}
