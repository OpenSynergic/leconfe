<?php

namespace App\Models;

use App\Models\Scopes\AnnouncementScope;

class Announcement extends UserContent
{
    protected static function booted(): void
    {
        static::addGlobalScope(new AnnouncementScope);
    }
}
