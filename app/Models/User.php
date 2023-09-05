<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Enums\ConferenceStatus;
use App\Models\Meta\UserMeta;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Kra8\Snowflake\HasShortflakePrimary;
use Laravel\Sanctum\HasApiTokens;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Squire\Models\Country;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasDefaultTenant, HasMedia, HasName, HasTenants
{
    use HasApiTokens, HasFactory, HasRoles, HasShortflakePrimary, InteractsWithMedia, Metable, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'given_name',
        'family_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected function getMetaClassName(): string
    {
        return UserMeta::class;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->family_name} {$this->given_name}"),
        );
    }

    public function getFilamentName(): string
    {
        return "{$this->family_name} {$this->given_name}";
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Replaced From original DatabaseNotification laravel
     * Get the entity's notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->latest();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($tenant->getKey() == Conference::current()?->getKey()) {
            return true;
        }

        if ($this->canAccessMultipleTenant()) {
            return true;
        }

        return false;
    }

    public function canAccessMultipleTenant(): bool
    {
        // TODO implement logic using spatie permissions

        return true;
    }

    /**
     * @return array<Model> | Collection
     */
    public function getTenants(Panel $panel): array|Collection
    {
        return Conference::query()
            ->with('media')
            ->where('status', '!=', ConferenceStatus::Archived)
            ->get();
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return Conference::current();
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Determine if the model may perform the given permission.
     *
     * @param  string|int|\Spatie\Permission\Contracts\Permission  $permission
     * @param  string|null  $guardName
     *
     * @throws PermissionDoesNotExist
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if ($this->getWildcardClass()) {
            return $this->hasWildcardPermission($permission, $guardName);
        }

        $permission = $this->filterPermission($permission, $guardName);

        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission) || $this->hasPermissionViaParentRole($permission);
    }

    public function hasPermissionViaParentRole(Permission $permission)
    {
        $this->loadMissing(['roles' => function (MorphToMany $query) {
            $query->with('ancestors');
        }]);

        foreach ($this->roles as $role) {
            if (! $role->parent_id) {
                continue;
            }

            if (! $role->ancestors->pluck('id')->intersect($permission->roles->pluck('id')->toArray())->isEmpty()) {
                return true;
            }
        }

        return false;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('profile', 'avatar');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('avatar')
            ->keepOriginalImageFormat()
            ->width(50);
    }
}
