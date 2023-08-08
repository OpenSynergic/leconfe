<?php

namespace App\Models;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Venue extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'location'];


    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('my-conversion')
            ->greyscale()
            ->quality(80)
            ->withResponsiveImages();
    }
}
