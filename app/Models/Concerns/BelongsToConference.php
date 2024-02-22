<?php

namespace App\Models\Concerns;

use App\Models\Conference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

trait BelongsToConference
{
    public static function bootBelongsToConference()
    {
        static::creating(function (Model $model) {
            $model->conference_id ??= App::getCurrentConferenceId();
        });
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }
}
