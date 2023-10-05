<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory, BelongsToConference;

    protected $fillable = [
        'title',
        'subtitle',
        'date',
        'roles',
        'conference_id'
    ];

    protected $casts = [
        'roles' => 'array',
        'date' => 'datetime'
    ];
}