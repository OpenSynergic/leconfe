<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kra8\Snowflake\HasShortflakePrimary;
use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
    use HasShortflakePrimary, Cachable;

    public function parent() : BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_id');
    }

}
