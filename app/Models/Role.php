<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Role extends Model
{
    use HasRecursiveRelationships;

    protected $fillable = [
        'parent_id',
        'name',
        'guard_name',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_id');
    }

    public function hasPermissionOnAncestors(Permission $permission)
    {
        return $this->ancestors->pluck('id')->intersect($permission->roles->pluck('id')->toArray())->isNotEmpty();
    }

    public function hasPermissionOnAncestorsAndSelf(Permission $permission)
    {
        return $this->ancestorsAndSelf->pluck('id')->intersect($permission->roles->pluck('id')->toArray())->isNotEmpty();
    }
}
