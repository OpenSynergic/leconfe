<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use App\Models\Concerns\BelongsToSerie;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Sponsor extends Model implements HasMedia, Sortable
{
    use HasFactory, InteractsWithMedia, BelongsToSerie, SortableTrait, Cachable;

    protected $fillable = [
        'name',
    ];

    // SpatieMediaLibraryImageColumn isn't working if this method is not exist
    public function registerMediaCollections(): void
    {
        $name = str($this->name)
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        $url = $this->hasMedia('logo')
            ? $this->getFirstMediaUrl('logo', 'small')
            : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=111827&font-size=0.33';

        $this->addMediaCollection('logo')->useFallbackUrl($url);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('small')
            ->performOnCollections('logo')
            ->keepOriginalImageFormat()
            ->height(50);

        $this->addMediaConversion('thumb-sm')
            ->performOnCollections('logo')
            ->keepOriginalImageFormat()
            ->width(100);

        $this->addMediaConversion('thumb')
            ->performOnCollections('logo')
            ->keepOriginalImageFormat()
            ->width(400);

        $this->addMediaConversion('thumb-xl')
            ->performOnCollections('logo')
            ->keepOriginalImageFormat()
            ->width(800);
    }

    public function buildSortQuery()
    {
        return static::query()
            ->where('serie_id', app()->getCurrentSerieId());
    }

}
