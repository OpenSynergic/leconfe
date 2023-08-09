<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'conference_id'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Topic $topic) {
            $topic->conference_id ??= Conference::current()?->id;
        });
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
