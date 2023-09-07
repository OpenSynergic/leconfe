<?php

namespace App\Models;

use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ConferenceType;
use App\Models\Meta\ConferenceMeta;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
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
    use HasFactory, Cachable, Metable, InteractsWithMedia, HasShortflakePrimary;

    protected static ?Conference $current;

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
        'path',
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

    protected function getMetaClassName(): string
    {
        return ConferenceMeta::class;
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

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function navigations(): HasMany
    {
        return $this->hasMany(Navigation::class);
    }

    public function getNavigationItems(string $handle): array
    {
        return $this->navigations->firstWhere('handle', $handle)?->items ?? [];
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
            ->keepOriginalImageFormat()
            ->width(50);

        $this->addMediaConversion('thumb')
            ->keepOriginalImageFormat()
            ->width(400);

        $this->addMediaConversion('thumb-xl')
            ->keepOriginalImageFormat()
            ->width(800);
    }

    public static function current(): ?self
    {
        if (!isset(static::$current)) {
            static::$current = static::where('status', ConferenceStatus::Current)->first();
        }

        return static::$current;
    }

    public static function upcoming()
    {
        return static::where('status', ConferenceStatus::Upcoming)->get();
    }


    public function isUpcoming()
    {
        return $this->status == ConferenceStatus::Upcoming;
    }

    public function isCurrent()
    {
        return $this->status == ConferenceStatus::Current;
    }
}
