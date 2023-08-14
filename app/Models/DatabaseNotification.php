<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Notifications\DatabaseNotification as Model;

class DatabaseNotification extends Model
{
    use Cachable;
}
