<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_folder',
        'version',
        'installed_at',
        'is_current',
    ];

    protected $casts = [
        'installed_at' => 'timestamp',
        'is_current' => 'boolean',
    ];

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public static function application()
    {
        return static::firstOrCreate([
            'product_name' => 'Leconfe',
            'product_folder' => 'leconfe',
            'is_current' => true,
        ], [
            'version' => app()->getCodeVersion(),
            'installed_at' => now(),
        ]);
    }
}
