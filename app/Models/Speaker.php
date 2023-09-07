<?php

namespace App\Models;

use Filament\Facades\Filament;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Speaker extends Model implements HasMedia, Sortable
{
    use Cachable, HasFactory, InteractsWithMedia, SortableTrait;

    protected $fillable = [
        'name',
        'affiliation',
        'description',
        'position',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Speaker $speaker) {
            $speaker->conference_id ??= Filament::getTenant()?->getKey();
        });
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
