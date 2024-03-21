<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Serie extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Metable;

    protected $fillable = [
        'path',
        'title',
        'description',
    ];
}
