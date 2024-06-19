<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function getUrl()
    {
        return $this->remote_url ?? route('submission-files.view', $this->file->media->uuid);
    }

    public function isPdf()
    {
        if($this->file->media->mime_type === 'application/pdf'){
            return true;
        }

        if($this->remove_url && Str::endsWith($this->remote_url, '.pdf')){
            return true;
        }

        return false;
    }
}
