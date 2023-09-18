<?php

namespace App\Models;

use App\Models\Scopes\AnnouncementTagScope;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AnnouncementTag extends Tag
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope(new AnnouncementTagScope);
    }

    public function announcements(): BelongsToMany
    {
        return $this->morphedByMany(Announcement::class, 'taggable', null, 'tag_id');
    }
}
