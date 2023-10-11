<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Scopes\ConferenceScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToConference;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public static function getTimelinesInRange()
    {

        return self::where('conference_id', app()->getCurrentConference()?->getKey())
            ->get();
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope(new ConferenceScope);
    // }
}
