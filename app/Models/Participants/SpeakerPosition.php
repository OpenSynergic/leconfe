<?php

namespace App\Models\Participants;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpeakerPosition extends ParticipantPosition
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('speaker', function (Builder $builder) {
            $builder->where('type', 'speaker');
        });

        static::creating(function (SpeakerPosition $model) {
            $model->type = 'speaker';
        });
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(Speaker::class, 'participant_position_id');
    }
}
