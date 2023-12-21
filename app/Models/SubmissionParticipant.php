<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionParticipant extends Model
{
    use HasFactory;

    protected $table = 'submission_has_participants';

    protected $fillable = [
        'submission_id',
        'user_id',
        'role_id',
    ];

    protected static function booted(): void
    {
        static::created(function (SubmissionParticipant $record) {
            activity('submission')
                ->performedOn($record->submission)
                ->causedBy(auth()->user())
                ->withProperties([
                    'user_id' => $record->user_id,
                    'role_id' => $record->role_id,
                ])
                ->log(__('log.participant.assigned', [
                    'name' => $record->user->fullName,
                    'role' => $record->role->name,
                ]));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
