<?php

namespace App\Models;

use App\Models\Enums\SerieState;
use App\Models\Enums\SerieType;
use App\Models\Meta\ConferenceMeta;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Vite;
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
        'status',
        'path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [

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

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
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
        return $this->hasMany(NavigationMenu::class);
    }

    public function getNavigationItems(string $handle): array
    {
        return $this->navigations->firstWhere('handle', $handle)?->items ?? [];
    }

    public function series(): HasMany
    {
        return $this->hasMany(Serie::class);
    }

    public function currentSerie() : HasOne
    {
        return $this->hasOne(Serie::class)->where('state', SerieState::Current);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function proceedings(): HasMany
    {
        return $this->hasMany(Proceeding::class);
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

    public function getPanelUrl(): string
    {
        return route('filament.conference.pages.dashboard', ['conference' => $this->path]);
    }

    public function getHomeUrl(): string
    {
        return route('livewirePageGroup.conference.pages.home', ['conference' => $this->path]);
    }

    public function getSupportedCurrencies(): array
    {
        return $this->getMeta('payment.supported_currencies') ?? ['usd'];
    }

    public function getThumbnailUrl(): string
    {
        return $this->getFirstMedia('thumbnail')?->getAvailableUrl(['thumb', 'thumb-xl']) ?? Vite::asset('resources/assets/images/placeholder-vertical.jpg');
    }
}
