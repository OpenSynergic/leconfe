<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerAssignedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'submission_file_id',
    ];

    public function reviews()
    {
        return $this->belongsTo(Review::class);
    }

    public function submissionFile()
    {
        return $this->belongsTo(SubmissionFile::class);
    }
}
