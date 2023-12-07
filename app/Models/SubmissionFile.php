<?php

namespace App\Models;

use App\Constants\SubmissionFileCategory;
use App\Notifications\SubmissionFileUploaded;
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
        'category',
    ];

    protected $with = ['type'];

    public static function booted()
    {
        static::creating(function (SubmissionFile $record) {
            $record->user_id = auth()->id();
        });

        static::created(function (SubmissionFile $createdModel) {
            // Send notification when there is new papers or revision uploaded
            // Should we created an event for this ?
            // for example SubmissionFilesUploaded, then we can listen to this event and send notification
            $shouldSendNotification = in_array(
                $createdModel->category,
                [
                    SubmissionFileCategory::PAPER_FILES,
                    SubmissionFileCategory::REVISION_FILES,
                ]
            );

            if ($shouldSendNotification) {
                $editors = $createdModel->submission->participants()
                    ->whereHas('role', function ($query) {
                        $query->where('name', 'editor');
                    })
                    ->get()
                    ->pluck('user_id');

                $editors = User::whereIn('id', $editors)->get();

                if ($editors->count()) {
                    $editors->each(function (User $editor) use ($createdModel) {
                        $editor->notify(new SubmissionFileUploaded($createdModel));
                    });
                }
            }
        });

        static::deleting(function (SubmissionFile $record) {
            if ($record->category == SubmissionFileCategory::PAPER_FILES) {
                $record->reviewerAssginedFiles()->delete();
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
