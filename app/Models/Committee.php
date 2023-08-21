<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Committee extends Model
{
    use HasFactory;
    protected $fillable = [
        'conference_id',
        'position',
        'description'

    ];

    protected static function booted(): void
    {
        static::creating(function (Committee $committee) {
            $committee->conference_id ??= Filament::getTenant()?->getKey();
        });
    }

    public function members()
    {
        return $this->hasMany(CommitteeMember::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}