<?php

namespace App\Models;

use App\Models\Meta\ParticipantMeta;
use Database\Factories\ParticipantFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Participant extends Model implements HasMedia, Sortable
{
    use HasFactory, HasShortflakePrimary, InteractsWithMedia, Metable, SortableTrait;

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
        // 'participant_position_id',
        'email',
        'given_name',
        'family_name',
        'public_name',
        'country',
    ];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::squish($this->given_name . ' ' . $this->family_name),
        );
    }

    protected static function newFactory(): Factory
    {
        return ParticipantFactory::new();
    }

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

    public function positions(): MorphToMany
    {
        return $this
            ->morphedByMany(ParticipantPosition::class, 'model', 'model_has_participants', 'participant_id', 'model_id');
    }

    public static function byEmail(string $email)
    {
        return static::whereEmail($email)->first();
    }
}
