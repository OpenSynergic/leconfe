<?php

namespace App\Models;

use App\Models\Scopes\AnnouncementScope;
use Filament\Facades\Filament;

class Announcement extends UserContent
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope(new AnnouncementScope);

        static::creating(function (UserContent $userContent) {
            // $userContent->conference_id ??= Filament::getTenant()?->getKey();
            dd($userContent);
        });
    }
}
