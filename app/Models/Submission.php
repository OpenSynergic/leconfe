<?php

namespace App\Models;

use App\Models\Enums\SubmissionStatus;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => SubmissionStatus::class,
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
