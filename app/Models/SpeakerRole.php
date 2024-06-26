<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSerie;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class SpeakerRole extends Model implements Sortable
{
    use BelongsToSerie, Cachable, HasFactory, SortableTrait;

    protected $table = 'speaker_roles';

    protected $fillable = [
        'serie_id',
        'name',
    ];

    public function speakers(): HasMany
    {
        return $this->hasMany(Speaker::class);
    }

    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function scopeByActiveSeries($query, $seriesId)
    {
        return $query->where('serie_id', $seriesId)
            ->whereHas('speakers')
            ->with(['speakers' => ['media', 'meta']]);
    }
}
