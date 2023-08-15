<?php

namespace App\Models;

use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ConferenceType;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Conference extends Model implements HasMedia, HasName, HasAvatar
{
    use HasFactory, Metable, InteractsWithMedia, HasShortflakePrimary;

    protected static Conference $current;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_current',
        'type',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => ConferenceStatus::class,
        'type' => ConferenceType::class,
    ];

    public static function current(): ?Conference
    {
        if (!isset(static::$current)) {
            static::$current = static::where('is_current', true)->first();
        }

        return static::$current;
    }

    protected static function booted(): void
    {
        static::deleting(function (Conference $conference) {
            if ($conference->getKey() == static::current()?->getKey()) {
                throw new \Exception('Conference cannot be deleted because it is currently set as current conference');
            }

            // TODO conference tidak bisa dihapus ketika ada data lain yg terkait dengan conference ini
        });
    }

    public function submission(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(Speaker::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'tenant');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('tenant')
            ->width(50);

        $this->addMediaConversion('thumb')
            ->width(400)
            ->sharpen(10);

        $this->addMediaConversion('thumb-xl')
            ->width(800)
            ->sharpen(10);
    }
}
