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

class AuthorRole extends Model implements Sortable
{
    use BelongsToConference, HasFactory, HasRecursiveRelationships, SortableTrait;

    protected $table = 'author_roles';

    protected $fillable = [
        'conference_id',
        'parent_id',
        'name',
    ];

    public function authors(): HasMany
    {
        return $this->hasMany(Author::class);
    }

    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }
}
