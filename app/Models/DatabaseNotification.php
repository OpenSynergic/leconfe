<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Kra8\Snowflake\HasShortflakePrimary;
use Illuminate\Notifications\DatabaseNotification as Model;

class DatabaseNotification extends Model
{
  use Cachable;
}
