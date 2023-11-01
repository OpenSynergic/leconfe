<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'submission_file_type_id',
        'media_id',
        'user_id',
        'category'
    ];

    protected $with = ['type'];

    public static function booted()
    {
        static::creating(function (SubmissionFile $record) {
            $record->user_id = auth()->id();
        });

        static::deleted(function (SubmissionFile $deletedModel) {
            if ($deletedModel->media()->exists()) {
                $deletedModel->media()->delete();
            }
        });
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function type()
    {
        return $this->belongsTo(SubmissionFileType::class, 'submission_file_type_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewerAssginedFiles(): HasMany
    {
        return $this->hasMany(ReviewerAssignedFile::class);
    }
}
