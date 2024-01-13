<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionTopicParticipant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'discussion_topic_id',
        'user_id',
    ];

    public function getRoleName(): string
    {
        $participant = $this->topic->submission->participants()->where('user_id', $this->user->getKey())->first();

        if (! $participant) {
            return 'Unassigned';
        }

        return $participant->role->name;
    }

    public function topic()
    {
        return $this->belongsTo(DiscussionTopic::class, 'discussion_topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
