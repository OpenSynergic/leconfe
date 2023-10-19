<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionContributor extends Model
{
    use HasFactory;

    protected $table = 'submission_has_contributors';

    protected $fillable = [
        'submission_id',
        'participant_id',
        'participant_position_id'
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function position()
    {
        return $this->belongsTo(ParticipantPosition::class, 'participant_position_id');
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
