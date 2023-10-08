<?php

namespace App\Models;

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

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
