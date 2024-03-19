<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class PluginSetting extends Model
{
    // use Cachable;

    protected $fillable = [
        'conference_id',
        'plugin',
        'key',
        'value',
        'type',
    ];
}
