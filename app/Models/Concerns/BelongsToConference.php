<?php

namespace App\Models\Concerns;

use App\Models\Conference;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToConference
{
    public static function bootBelongsToConference()
    {
        static::creating(function (Model $model) {
            $model->conference_id ??= app()->getCurrentConference()?->getKey() ?? Filament::getTenant();
        });
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }
}
