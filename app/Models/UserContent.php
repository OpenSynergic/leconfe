<?php

namespace App\Models;

use App\Models\Meta\UserContentMeta;
use DateTimeInterface;
use Filament\Facades\Filament;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

class UserContent extends Model implements HasMedia
{
    use HasFactory, HasTags, HasSlug, Cachable, Metable, InteractsWithMedia;

    protected $table = 'user_contents';

    protected $fillable = [
        'title',
        'slug',
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

    protected static function booted(): void
    {
        static::creating(function (UserContent $userContent) {
            $userContent->conference_id ??= Filament::getTenant()?->getKey();
        });
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected function getMetaClassName(): string
    {
        return UserContentMeta::class;
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(setting('format.date'));
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
