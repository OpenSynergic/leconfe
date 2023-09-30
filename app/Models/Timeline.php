<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;

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

    protected static function booted(): void
    {
        static::creating(function (Timeline $timeline) {
            $timeline->conference_id ??= app()->getCurrentConference()?->getKey();
        });
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function scopeForConference($query)
    {
        return $query->where('conference_id', app()->getCurrentConference()?->getKey())->orderBy('date');
    }

    public static function getTimelinesForCurrentConference()
    {
        return self::forConference();
    }
}
