<?php

namespace App\Models\Participants;

use App\Models\Concerns\BelongsToConference;
use App\Models\Meta\ParticipantMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Participant extends Model implements HasMedia, Sortable
{
    use BelongsToConference, HasFactory, HasShortflakePrimary, InteractsWithMedia, SortableTrait, Metable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'participants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conference_id',
        'participant_position_id',
        'type',
        'given_name',
        'family_name',
        'public_name',
        'country',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('avatar')
            ->keepOriginalImageFormat()
            ->width(50);

        $this->addMediaConversion('thumb')
            ->keepOriginalImageFormat()
            ->width(400);

        $this->addMediaConversion('thumb-xl')
            ->keepOriginalImageFormat()
            ->width(800);
    }

    protected function getMetaClassName(): string
    {
        return ParticipantMeta::class;
    }
}
