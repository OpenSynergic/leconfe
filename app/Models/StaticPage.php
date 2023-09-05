<?php

namespace App\Models;

use App\Models\Meta\StaticPageMeta;
use DateTimeInterface;
use Filament\Facades\Filament;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Metable\Metable;

class StaticPage extends Model
{
    use HasFactory, Cachable, Metable;

    protected $table = 'user_contents';

    protected $fillable = [
        'title',
        'content_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'expires_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (StaticPage $staticPage) {
            $staticPage->conference_id ??= Filament::getTenant()?->getKey();
        });
    }

    protected function getMetaClassName(): string
    {
        return StaticPageMeta::class;
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(setting('format.date'));
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
