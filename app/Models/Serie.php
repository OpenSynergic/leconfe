<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Serie extends Model implements HasMedia
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


}
