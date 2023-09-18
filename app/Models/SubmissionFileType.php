<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionFileType extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'name',
    ];

    public static function booted()
    {
        static::saving(function (Model $record) {
            $record->conference_id = $record->conference_id ?? Conference::current()->getKey();
        });
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
