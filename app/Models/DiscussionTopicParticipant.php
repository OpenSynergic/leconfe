<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionTopicParticipant extends Model
{
    protected $table = 'discussion_topic_participants';

    use HasFactory;

    protected $fillable = [
        'discussion_topic_id',
        'user_id',
    ];

    public function topic()
    {
        return $this->belongsTo(DiscussionTopic::class, 'discussion_topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
