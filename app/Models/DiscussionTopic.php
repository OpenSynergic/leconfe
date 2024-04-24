<?php

namespace App\Models;

use App\Facades\Settings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DiscussionTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stage',
        'name',
        'user_id',
        'open',
    ];

    protected $casts = [
        'open' => 'boolean',
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

    public function getLastDiscussions()
    {
        return $this->discussions()->orderBy('created_at', 'desc')->first();
    }

    public function getLastSender()
    {
        if (!$discussions = $this->getLastDiscussions()) {
            return null;
        }

        return $discussions->user;
    }

    public function getLastUpdate(): ?string
    {
        if (!$discussions = $this->getLastDiscussions()) {
            return null;
        }

        return $discussions->updated_at->format(Settings::get('date') . ' ' . Settings::get('time'));
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

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
