<?php

namespace App\Models;

use App\Models\Traits\HasTopics;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Submission extends Model implements HasMedia
{
    use HasFactory, HasTags, HasTopics, Metable, HasShortflakePrimary, InteractsWithMedia, Cachable;

    const STATUS_WIZARD = 1;
    const STATUS_ACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'submission_progress',
        'status'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Submission $submission) {
            $submission->user_id ??= Auth::id();
            $submission->conference_id ??= Conference::current()?->id;
        });
    }

    public function authors()
    {
        return $this->hasMany(Author::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
