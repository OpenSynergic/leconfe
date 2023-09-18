<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionFileType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public static function nameById(int $id): string
    {
        return static::find($id)->name;
    }
}
