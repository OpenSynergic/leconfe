<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Kra8\Snowflake\HasShortflakePrimary;
use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
    use HasShortflakePrimary, Cachable;

    public const ADMIN = 'Admin';

    public const AUTHOR = 'Author';

    public const EDITOR = 'Editor';

    public const REVIEWER = 'Reviewer';
}
