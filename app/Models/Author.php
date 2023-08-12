<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Author extends Model implements Sortable
{
    use HasFactory, Cachable, Metable, SortableTrait, HasShortflakePrimary;

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

                return $this->getMeta('given_name').' '.$this->getMeta('family_name');
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
