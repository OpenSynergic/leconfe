<?php

namespace App\Models;

use Filament\Facades\Filament;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory, Cachable;

    protected $fillable = [
        'name',
        'slug',
        'conference_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Topic $topic) {
            $topic->conference_id ??= Filament::getTenant()?->getKey();
        });
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
