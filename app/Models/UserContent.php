<?php

namespace App\Models;

use App\Facades\Setting;
use App\Models\Concerns\BelongsToConference;
use DateTimeInterface;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

class UserContent extends Model implements HasMedia
{
    use BelongsToConference, Cachable, HasFactory, HasSlug, HasTags, InteractsWithMedia, Metable;

    protected $table = 'user_contents';

    protected $fillable = [
        'title',
        'slug',
        'created_by',
        'content_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'expires_at' => 'date',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->keepOriginalImageFormat()
            ->width(400);
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(Setting::get('format_date'));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOrWhereMeta(Builder $q, string $key, $operator, $value = null): void
    {
        // Shift arguments if no operator is present.
        if (! isset($value)) {
            $value = $operator;
            $operator = '=';
        }

        // Convert value to its serialized version for comparison.
        if (! is_string($value)) {
            $value = $this->makeMeta($key, $value)->getRawValue();
        }

        $q->orWhereHas('meta', function (Builder $q) use ($key, $operator, $value) {
            $q->where('key', $key);
            $q->where('value', $operator, $value);
        });
    }
}
