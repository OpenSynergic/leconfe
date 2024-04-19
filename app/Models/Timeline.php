<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use App\Models\Concerns\BelongsToSerie;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use BelongsToSerie, Cachable, HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'date',
        'roles',
        'serie_id',
    ];

    protected $casts = [
        'roles' => 'array',
        'date' => 'datetime',
    ];
}
