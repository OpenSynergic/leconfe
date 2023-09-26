<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use BelongsToConference, HasFactory, Cachable;

    protected $fillable = [
        'class',
        'conference_id',
        'position',
        'sort',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
