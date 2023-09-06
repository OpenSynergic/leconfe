<?php

namespace App\Models\Participants;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Speaker extends Participant
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('speaker', function (Builder $builder) {
            $builder->where('type', 'speaker');
        });

        static::creating(function (Model $model) {
            $model->type = 'speaker';
        });
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(SpeakerPosition::class, 'participant_position_id');
    }
}
