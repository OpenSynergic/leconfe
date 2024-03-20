<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class CommitteeRole extends Model implements Sortable
{
    use BelongsToConference, HasFactory, HasRecursiveRelationships, HasSlug, SortableTrait;

    protected $table = 'committee_roles';

    protected $fillable = [
        'conference_id',
        'parent_id',
        'type',
        'name',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function committees(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }
}
