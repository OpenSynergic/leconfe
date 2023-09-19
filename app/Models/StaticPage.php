<?php

namespace App\Models;

use App\Models\Scopes\StaticPageScope;

class StaticPage extends UserContent
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope(new StaticPageScope);
    }
}
