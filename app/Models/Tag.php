<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends \Spatie\Tags\Tag
{
    use HasFactory;

    protected $table = 'tags';

    protected $casts = [
        'name' => 'array',
    ];

    protected $fillable = [
        'name',
    ];

    public static function whereInFromString(array $array, ?string $type = null)
    {
        return collect(array_map(fn ($tag) => static::findFromString($tag, $type), $array));
    }
}
