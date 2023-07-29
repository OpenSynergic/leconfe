<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Spatie\MediaLibrary\MediaCollections\Models\Media as Model;

class Media extends Model
{
  use Cachable;
}
