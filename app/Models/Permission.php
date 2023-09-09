<?php

namespace App\Models;

use App\Models\Concerns\HasVirtualColumns;
use Spatie\Permission\Models\Permission as Model;

class Permission extends Model
{
    use HasVirtualColumns;

    /**
     * The virtual generated columns on the model
     *
     * @var array
     */
    protected $virtualColumns = [
        'context',
        'action',
    ];

    protected static function booting(): void
    {
        static::deleting(function (Permission $permission) {
            $permission->loadMissing('roles');
            if ($permission->roles()->exists()) {
                throw new \Exception('Permission cannot be deleted because it is currently assigned to a roles');
            }
        });
    }
}
