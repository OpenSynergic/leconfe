<?php

namespace App\Models;

use App\Models\Enums\ContentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AnnouncementTag extends Tag
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('announcement', function (Builder $builder) {
            $builder->where('type', ContentType::Announcement->value);
        });
    }

    public function announcements(): BelongsToMany
    {
        return $this->morphedByMany(Announcement::class, 'taggable', null, 'tag_id');
    }
}
