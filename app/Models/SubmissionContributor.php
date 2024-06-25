<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionContributor extends Model
{
    use HasFactory;

    protected $table = 'submission_has_contributors';

    protected $fillable = [
        'submission_id',
        'contributor_id',
        'contributor_type',
        'contributor_role_id',
    ];

    public function contributor()
    {
        return $this->morphTo();
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
