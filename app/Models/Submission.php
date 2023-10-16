<?php

namespace App\Models;

use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\Submissions\SubmissionAssignParticipantAction;
use App\Constants\SubmissionFileCategory;
use App\Models\Concerns\HasTopics;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Meta\SubmissionMeta;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
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
        'skipped_review',
        'stage',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'stage' => SubmissionStage::class,
        'status' => SubmissionStatus::class,
        'skipped_review' => 'boolean'
    ];

    // public function getField()
    // {
    //     return match($this->form_type) {
    //         'radio' => Radio::make(),
    //     }
    // }

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
            $submission->conference_id ??= app()->getCurrentConference()?->getKey();
        });

        static::created(function (Submission $submission) {
            if ($user = Auth::user()) {
                $participant = Participant::email($user->email)->first();
                $participant = $participant ?: ParticipantCreateAction::run(
                    $user->only('email', 'given_name', 'family_name', 'public_name', 'country'),
                );
                $positionAuthor = ParticipantPosition::where('type', 'author')->first();
                $participant->positions()->detach($positionAuthor);
                $participant->positions()->attach($positionAuthor);
                SubmissionAssignParticipantAction::run($submission, $participant, $positionAuthor);
            }
        });
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
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
        return $this->media()->where('collection_name', SubmissionFileCategory::FILES);
    }

    public function papers()
    {
        return $this->media()->where('collection_name', SubmissionFileCategory::FILES);
    }

    public function participants()
    {
        return $this->hasMany(SubmissionParticipant::class);
    }

    public function scopeStage(Builder $query, SubmissionStage $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeStatus(Builder $query, SubmissionStatus $status)
    {
        return $query->where('status', $status);
    }
}
