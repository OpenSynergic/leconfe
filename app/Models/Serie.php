<?php

namespace App\Models;

use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasAvatar;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Concerns\BelongsToConference;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Serie extends Model implements HasMedia, HasAvatar, HasName
{
    use Cachable, BelongsToConference, HasFactory, InteractsWithMedia, Metable, SoftDeletes;

    protected $fillable = [
        'path',
        'title',
        'description',
        'issn',
        'date_start',
        'date_end',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    public function conference() : BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function getPanelUrl(): string
    {
        return route('filament.series.pages.dashboard', ['serie' => $this->path]);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'tenant');
    }

    public function getFilamentName(): string
    {
        return $this->title;
    }
}
