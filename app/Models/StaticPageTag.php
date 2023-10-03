<?php

namespace App\Models;

use App\Models\Enums\ContentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StaticPageTag extends Tag
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('announcement', function (Builder $builder) {
            $builder->where('type', ContentType::StaticPage->value);
        });
    }

    public function staticPages(): BelongsToMany
    {
        return $this->morphedByMany(StaticPage::class, 'taggable', null, 'tag_id');
    }
}
