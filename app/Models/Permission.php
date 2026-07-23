<?php

namespace App\Models;

use Exception;
use App\Models\Enums\UserRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as Model;
use Spatie\Permission\PermissionRegistrar;

class Permission extends Model
{
    protected function context(): Attribute
    {
        [$context, $action] = explode(':', $this->name);

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
                throw new Exception('Permission cannot be deleted because it is currently assigned to a roles');
            }
        });
    }

    public static function getProtectedPermissionContexts()
    {
        return ! auth()->user()?->hasRole(UserRole::Admin) ? [
            'Administration',
            'Plugin',
            'Panel',
        ] : [];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            app(PermissionRegistrar::class)->pivotRole
        )->withoutGlobalScopes();
    }
}
