<?php

namespace App\Models;

use App\Models\Concerns\HasTopics;
use App\Models\Enums\SubmissionStatus;
use App\Models\Meta\SubmissionMeta;
use Filament\Facades\Filament;
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
    use Cachable, HasFactory, HasShortflakePrimary, HasTags, HasTopics, InteractsWithMedia, Metable;

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

    protected function getMetaClassName(): string
    {
        return SubmissionMeta::class;
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Submission $submission) {
            $submission->user_id ??= Auth::id();
            $submission->conference_id ??= Filament::getTenant()?->getKey();
        });
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->media()->where('collection_name', 'submission-files');
    }

    public function participants()
    {
        return $this->hasMany(SubmissionParticipant::class);
    }
}
