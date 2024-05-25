<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SubmissionGalley extends Model implements HasMedia, Sortable
{
    use SortableTrait, InteractsWithMedia;

    protected $table = 'submission_galleys';

    protected $fillable = [
        'label',
        'remote_url',
        'submission_id',
        'submission_file_id',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function file()
    {
        return $this->belongsTo(SubmissionFile::class, 'submission_file_id');
    }
}
