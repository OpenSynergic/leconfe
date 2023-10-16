<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kra8\Snowflake\HasShortflakePrimary;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Review extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasShortflakePrimary;

    protected $casts = [
        'date_assigned' => 'datetime',
        'date_confirmed' => 'datetime',
        'date_completed' => 'datetime'
    ];

    protected $fillable = [
        'submission_id',
        'participant_id',
        'status',
        'recommendation',
        'date_assigned',
        'date_confirmed',
        'date_completed',
        'quality',
        'review_author_editor',
        'review_editor'
    ];

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function files()
    {
        return $this->media()->where('collection_name', 'reviewer-assigned-papers');
    }

    public function scopeSubmission($query, int $submissionId)
    {
        return $query->where('submission_id', $submissionId);
    }

    public function scopeParticipant($query, int $participantId)
    {
        return $query->where('participant_id', $participantId);
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function needConfirmation(): bool
    {
        return is_null($this->date_confirmed);
    }
}
