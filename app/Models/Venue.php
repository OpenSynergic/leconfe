<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Venue extends Model implements HasMedia
{
    use BelongsToConference, Cachable, HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'location', 'description'];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
