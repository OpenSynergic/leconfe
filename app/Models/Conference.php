<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Conference extends Model implements HasMedia
{
    use HasFactory, Cachable, Metable, InteractsWithMedia, HasShortflakePrimary;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
    ];

   

    public static function current()
    {
        return static::find(setting('current_conference'));
    }

    protected static function booted(): void
    {
        static::deleting(function (Conference $conference) {
            // TODO conference tidak bisa dihapus ketika ada data lain yg terkait dengan conference ini
        });
    }

    public function submission(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
