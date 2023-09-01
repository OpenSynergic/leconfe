<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'class',
        'conference_id',
        'position',
        'sort',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (Block $block) {
            $block->conference_id ??= Conference::current()?->getKey();
        });
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
