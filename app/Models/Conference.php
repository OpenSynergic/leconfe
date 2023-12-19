<?php

namespace App\Models;

use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ConferenceType;
use App\Models\Meta\ConferenceMeta;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Conference extends Model implements HasAvatar, HasMedia, HasName
{
    use Cachable, HasFactory, HasShortflakePrimary, HasSlug, InteractsWithMedia, Metable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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

    public function submission(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(Timeline::class);
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

    public function staticPages(): HasMany
    {
        return $this->hasMany(StaticPage::class);
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

    public function registerMediaConversions(?Media $media = null): void
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

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('path')
            ->skipGenerateWhen(fn () => $this->path !== null);
    }

    public static function active(): ?self
    {
        return static::where('status', ConferenceStatus::Active)->first();
    }

    public function scopeUpcoming(Builder $query)
    {
        return $query
            ->with(['meta'])
            ->whereHasMeta('date_held')
            ->orderByMetaNumeric('date_held', 'asc')
            ->where('status', ConferenceStatus::Upcoming);
    }

    public function isUpcoming(): bool
    {
        return $this->status == ConferenceStatus::Upcoming;
    }

    public function isActive(): bool
    {
        return $this->status == ConferenceStatus::Active;
    }

    public function getHomeUrl(): string
    {
        return match ($this?->status) {
            ConferenceStatus::Active => route('livewirePageGroup.current-conference.pages.home'),
            ConferenceStatus::Archived => route('livewirePageGroup.archive-conference.pages.home', ['conference' => $this->path]),
            default => route('livewirePageGroup.website.pages.home'),
        };
    }

    public function getSupportedCurrencies(): array
    {
        return $this->getMeta('workflow.payment.supported_currencies') ?? ['usd'];
    }
}
