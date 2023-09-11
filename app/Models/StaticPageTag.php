<?php

namespace App\Models;

use App\Models\Scopes\StaticPageTagScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StaticPageTag extends Tag
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope(new StaticPageTagScope);
    }

    public function staticPages(): BelongsToMany
    {
        return $this->morphedByMany(StaticPage::class, 'taggable', null, 'tag_id');
    }
}