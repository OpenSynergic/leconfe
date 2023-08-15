<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use RyanChandler\FilamentNavigation\Models\Navigation as Model;


class Navigation extends Model
{
    use Cachable;

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
