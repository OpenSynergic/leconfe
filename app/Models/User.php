<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasName;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Squire\Models\Country;

class User extends Authenticatable implements HasName
{
    use HasApiTokens, HasFactory, Notifiable, Metable, HasShortflakePrimary, Cachable;

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
}
