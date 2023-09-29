<?php

namespace App\Models\Concerns;

use App\Application;
use App\Models\Conference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToConference
{
    public static function bootBelongsToConference()
    {
        static::creating(function (Model $model) {
            $model->conference_id ??= app()->getCurrentConference()?->getKey() ?? Application::CONTEXT_WEBSITE;
        });
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }
}
