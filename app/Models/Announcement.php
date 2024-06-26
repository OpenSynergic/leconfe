<?php

namespace App\Models;

use App\Models\Enums\ContentType;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Announcement extends UserContent
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

    public function getUrl()
    {
        return route('livewirePageGroup.conference.pages.announcement-page', [
            'conference' => $this->conference->path,
            'announcement' => $this->id,
        ]);
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
