<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class SubmissionContributor extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'submission_has_contributors';

    protected $fillable = [
        'submission_id',
        'contributor_id',
        'contributor_type',
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
