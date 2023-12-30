<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'open'
    ];

    protected $casts = [
        'open' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function participants()
    {
        return $this->hasMany(DiscussionTopicParticipant::class);
    }
}
