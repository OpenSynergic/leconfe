<?php

namespace App\Models;

use App\Actions\User\CreateParticipantFromUserAction;
use App\Models\Concerns\HasTopics;
use App\Models\Concerns\InteractsWithPayment;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Models\Interfaces\HasPayment;
use App\Models\Meta\SubmissionMeta;
use App\Models\States\Submission\BaseSubmissionState;
use App\Models\States\Submission\DeclinedSubmissionState;
use App\Models\States\Submission\EditingSubmissionState;
use App\Models\States\Submission\IncompleteSubmissionState;
use App\Models\States\Submission\OnReviewSubmissionState;
use App\Models\States\Submission\PublishedSubmissionState;
use App\Models\States\Submission\QueuedSubmissionState;
use App\Models\States\Submission\WithdrawnSubmissionState;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\Activitylog\Models\Activity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Submission extends Model implements HasMedia, HasPayment
{
    use Cachable, HasFactory, HasShortflakePrimary, HasTags, HasTopics, InteractsWithMedia, InteractsWithPayment, Metable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'skipped_review',
        'stage',
        'status',
        'revision_required',
        'withdrawn_reason',
        'withdrawn_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'stage' => SubmissionStage::class,
        'status' => SubmissionStatus::class,
        'skipped_review' => 'boolean',
        'revision_required' => 'boolean',
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
        static::addGlobalScope('user', function (Builder $builder) {
            // $currentUser = auth()->user();
            // if (
            //     $currentUser->hasRole(UserRole::Editor->value)
            //     || $currentUser->hasRole(UserRole::Reviewer->value)
            // ) {
            //     $builder->where('user_id', auth()->id());
            // }
        });

        static::creating(function (Submission $submission) {
            $submission->user_id ??= Auth::id();
            $submission->conference_id ??= app()->getCurrentConference()?->getKey();
        });

        static::deleting(function (Submission $submission) {
            $submission->participants()->delete();
            $submission->contributors()->delete();
            $submission->reviews()->delete();
            $submission->media()->delete();
        });

        static::created(function (Submission $submission) {
            $submission->participants()->create([
                'user_id' => $submission->user_id,
                'role_id' => Role::where('name', UserRole::Author->value)->first()->getKey(),
            ]);

            //If current user does not exists in participant
            if (!$userAsParticipant = $submission->user->asParticipant()) {
                $userAsParticipant = CreateParticipantFromUserAction::run($submission->user);
            }

            // Current user as a contributors
            $submission->contributors()->create([
                'participant_id' => $userAsParticipant->getKey(),
                'participant_position_id' => ParticipantPosition::where('name', UserRole::Author->value)->first()->getKey(),
            ]);
        });
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function reviewerAssignedFiles(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
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

    public function submissionFiles()
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function participants()
    {
        return $this->hasMany(SubmissionParticipant::class);
    }

    public function contributors()
    {
        return $this->hasMany(SubmissionContributor::class);
    }

    public function scopeStage(Builder $query, SubmissionStage $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeStatus(Builder $query, SubmissionStatus $status)
    {
        return $query->where('status', $status);
    }

    public function isPublished(): bool
    {
        return $this->status == SubmissionStatus::Published;
    }

    public function isDeclined(): bool
    {
        return $this->status == SubmissionStatus::Declined;
    }

    public function isIncomplete(): bool
    {
        return $this->status == SubmissionStatus::Incomplete;
    }

    /**
     * Get all the editors of this submission
     */
    public function getEditors(): Collection
    {
        return $this->participants()
            ->whereHas('role', function ($query) {
                $query->where('name', UserRole::Editor->value);
            })
            ->get()
            ->pluck('user_id')
            ->map(fn ($userId) => User::find($userId));
    }

    public function state(): BaseSubmissionState
    {
        return match ($this->status) {
            SubmissionStatus::Incomplete => new IncompleteSubmissionState($this),
            SubmissionStatus::Queued => new QueuedSubmissionState($this),
            SubmissionStatus::OnReview => new OnReviewSubmissionState($this),
            SubmissionStatus::Editing => new EditingSubmissionState($this),
            SubmissionStatus::Published => new PublishedSubmissionState($this),
            SubmissionStatus::Declined => new DeclinedSubmissionState($this),
            SubmissionStatus::Withdrawn => new WithdrawnSubmissionState($this),
            default => throw new \Exception('Invalid submission status'),
        };
    }

    public function hasPaymentProcess(): bool
    {
        return $this->conference->getMeta('payment.enabled') && match ($this->status) {
            SubmissionStatus::OnReview, SubmissionStatus::Editing, SubmissionStatus::Published => true,
            SubmissionStatus::Incomplete, SubmissionStatus::Queued, SubmissionStatus::Withdrawn, SubmissionStatus::Declined => false,
            default => false,
        };
    }
}
