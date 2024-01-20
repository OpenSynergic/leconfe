<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    use Cachable;

    protected $fillable = [
        'product_name',
        'product_folder',
        'version',
    ];

    protected $casts = [
        'installed_at' => 'timestamp',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Version $version) {
            $version->installed_at = now();
        });
    }

    public static function application()
    {
        $version = static::query()
            ->where('product_name', 'Leconfe')
            ->where('product_folder', 'leconfe')
            ->orderBy('installed_at', 'desc')
            ->first();

        if (! $version) {
            $version = app()->getVersion();
            $version->save();
        }

        return $version;
    }
}
