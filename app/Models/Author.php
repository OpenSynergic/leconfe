<?php

namespace App\Models;

use Plank\Metable\Metable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Author extends Model implements Sortable
{
    use HasFactory, Cachable, Metable, SortableTrait;

    protected $fillable = [
        'email',
        'submission_id',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    protected function publicName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (filled($this->getMeta('public_name'))) {
                    return $this->getMeta('public_name');
                }
                return $this->getMeta('given_name') . ' ' . $this->getMeta('family_name');
            }
        );
    }

    protected function affiliation(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->hasMeta('affiliation') && filled($this->getMeta('affiliation'))) {
                    return $this->getMeta('affiliation');
                }
                return null;
            }
        );
    }
}
