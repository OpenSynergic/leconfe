<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kra8\Snowflake\HasShortflakePrimary;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ReviewAssignment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasShortflakePrimary;

    protected $casts = [
        'date_assigned' => 'datetime',
        'date_confirmed' => 'datetime',
        'date_completed' => 'datetime',
        'canceled' => 'boolean',
    ];

    protected $fillable = [
        'submission_id',
        'participant_id',
        'recommendation',
        'date_assigned',
        'date_confirmed',
        'date_completed',
        'quality',
        'canceled'
    ];

    public function files()
    {
        return $this->media()->where('collection_name', 'reviewer-assigned-papers');
    }

    public function scopeSubmission($query, $submissionId)
    {
        return $query->where('submission_id', $submissionId);
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
        return $this->date_confirmed->format('Y') == '-0001';
    }
}
