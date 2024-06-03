<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Plank\Metable\Meta as Model;

class Meta extends Model
{
    // temporarily disable caching because it is not compatible with the plank/metable package
    // use Cachable;
}
