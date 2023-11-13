<?php

namespace App\Models;

use App\Constants\SubmissionFileCategory;
use App\Mail\Templates\NewPaperUploadedMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;

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

        static::created(function (SubmissionFile $createdModel) {
            // Send notification when there is new papers uploaded
            if ($createdModel->category == SubmissionFileCategory::PAPER_FILES) {
                $editors = $createdModel->submission->participants()->whereHas('role', function ($query) {
                    $query->where('name', 'editor');
                })->get()->pluck('user_id');
                $editors = User::whereIn('id', $editors)->get();
                if ($editors->count()) {
                    Mail::to($editors)->send(new NewPaperUploadedMail($createdModel));
                }
            }
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
