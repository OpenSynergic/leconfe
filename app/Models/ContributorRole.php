<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ContributorRole extends Model implements Sortable
{
    use Cachable, BelongsToConference, HasFactory, SortableTrait;

    protected $table = 'contributor_roles';

    protected $fillable = [
        'conference_id',
        'parent_id',
        'name',
    ];

    public function authors(): HasMany
    {
        return $this->hasMany(Contributor::class);
    }

    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }
}
