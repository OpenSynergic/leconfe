<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSerie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class CommitteeRole extends Model implements Sortable
{
    use BelongsToSerie, HasFactory, SortableTrait;

    protected $table = 'committee_roles';

    protected $fillable = [
        'serie_id',
        'parent_id',
        'name',
    ];

    public function committees(): HasMany
    {
        return $this->hasMany(Committee::class);
    }

    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function scopeByActiveSeries($query, $seriesId)
    {
        return $query->where('serie_id', $seriesId)
            ->whereHas('committees')
            ->with(['committees' => ['media', 'meta']]);
    }
}
