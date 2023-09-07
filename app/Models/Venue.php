<?php

namespace App\Models;

use Filament\Facades\Filament;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Venue extends Model implements HasMedia
{
    use Cachable, HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'location', 'description'];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Venue $venue) {
            $venue->conference_id ??= Filament::getTenant()?->getKey();
        });
    }
}
