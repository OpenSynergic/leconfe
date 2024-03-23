<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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
