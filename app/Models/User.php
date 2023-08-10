<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\HasDefaultTenant;
use Plank\Metable\Metable;
use Squire\Models\Country;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Notifications\Notifiable;
use Kra8\Snowflake\HasShortflakePrimary;
use Illuminate\Database\Eloquent\Casts\Attribute;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasName, HasTenants, HasDefaultTenant
{
    use HasApiTokens, HasFactory, Notifiable, Metable, HasShortflakePrimary, HasRoles;

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
    ];

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
        if ($tenant->getKey() == Conference::current()->getKey()) {
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
    public function getTenants(Panel $panel): array | Collection
    {
        return Conference::all();
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return Conference::current();
    }
}
