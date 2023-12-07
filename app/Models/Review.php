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
        'user_id',
        'status',
        'recommendation',
        'date_assigned',
        'date_confirmed',
        'date_completed',
        'quality',
        'review_author_editor',
        'review_editor'
    ];

    protected static function booted(): void
    {
        static::saving(function (Model $record) {
            if ($record->recommendation) {
                $record->date_completed = now();
            }
        });
    }

    public function reviewSubmitted(): bool
    {
        return !is_null($this->recommendation) && !is_null($this->date_completed);
    }

    public function assignedFiles()
    {
        return $this->hasMany(ReviewerAssignedFile::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // public function scopeBySubmission($query, int $submissionId)
    // {
    //     return $query->where('submission_id', $submissionId);
    // }

    public function scopeUser($query, User $user)
    {
        return $query->where('user_id', $user->getKey());
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmed(): bool
    {
        return !$this->needConfirmation();
    }

    public function needConfirmation(): bool
    {
        return is_null($this->date_confirmed);
    }
}
