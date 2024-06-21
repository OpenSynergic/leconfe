<?php

namespace App\Models;

use App\Models\Concerns\HasVirtualColumns;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Models\Permission as Model;

class Permission extends Model
{
    protected function context(): Attribute
    {
        [$context, $action] = explode(':', $this->name);
        // dd($context);
        return Attribute::make(
            get: fn () => $context,
        );
    }

    protected function action(): Attribute
    {
        [$context, $action] = explode(':', $this->name);

        return Attribute::make(
            get: fn () => $action,
        );
    }

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
