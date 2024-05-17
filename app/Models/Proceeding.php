<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use App\Models\Concerns\HasDOI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Proceeding extends Model implements HasMedia, Sortable
{
    use HasFactory, InteractsWithMedia, BelongsToConference, SortableTrait, HasDOI;

    protected $table = 'proceedings';

    protected $fillable = [
        'title',
        'description',
        'volume',
        'number',
        'year',
        'subject',
        'isbn',
        'published',
        'published_at',
        'current',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
        'current' => 'boolean',
    ];

    public function scopePublished($query, $published = true)
    {
        return $query->where('published', $published);
    }

    public function scopeCurrent($query, $current = true)
    {
        return $query->where('current', $current);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar')
            ->keepOriginalImageFormat()
            ->width(50);

        $this->addMediaConversion('thumb')
            ->keepOriginalImageFormat()
            ->width(400);

        $this->addMediaConversion('thumb-xl')
            ->keepOriginalImageFormat()
            ->width(800);
    }

    public function publish($published = true) : self
    {
        $this->published = $published;
        $this->published_at = $published ? now() : null;
        $this->save();

        $this->setAsCurrent();

        return $this;
    }

    public function unpublish() : self
    {
        return $this->publish(false);
    }

    public function setAsCurrent() : self
    {
        // Current only one for each conference
        $this->newQuery()->where('conference_id', $this->conference_id)->update(['current' => false]);

        $this->current = true;
        $this->save();

        return $this;
    }

    public function submissions() : HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function seriesTitle() : string
    {
        return 
            ($this->volume ? "Vol. {$this->volume}" : '').
            ($this->number ? " No. {$this->number}" : '').
            ($this->year ? " ({$this->year})" : '').
            ': '.$this->title;
    }
}
