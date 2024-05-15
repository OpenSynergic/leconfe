<?php

namespace App\Models\Meta;

use App\Models\Meta;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class ConferenceMeta extends Meta
{
    use Cachable;

    protected $table = 'conference_meta';
}
