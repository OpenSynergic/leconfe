<?php

namespace App\Models;

use App\Models\Enums\ContentType;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends UserContent
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('announcement', function (Builder $builder) {
            $builder->where('content_type', ContentType::Announcement->value);
        });
    }
}
