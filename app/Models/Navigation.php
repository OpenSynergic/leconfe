<?php

namespace App\Models;

use App\Application;
use App\Models\Concerns\BelongsToConference;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use RyanChandler\FilamentNavigation\Models\Navigation as Model;

class Navigation extends Model
{
    use BelongsToConference, Cachable;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Navigation $navigation) {
            $navigation->conference_id ??= Application::CONTEXT_WEBSITE;
        });
    }
}
