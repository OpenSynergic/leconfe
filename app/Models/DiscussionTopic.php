<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DiscussionTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage',
        'name',
        'user_id',
        'open'
    ];

    protected $casts = [
        'open' => 'boolean'
    ];

    protected static function booted()
    {
        static::creating(function ($topic) {
            $topic->user_id = Auth::id();
        });

        static::deleting(function ($topic) {
            $topic->discussions()->delete();
            $topic->participants()->delete();
        });
    }

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
