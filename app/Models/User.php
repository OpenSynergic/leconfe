<?php

namespace App\Models;

use App\Mail\Templates\VerifyUserEmail;
use App\Models\Enums\UserRole;
use App\Models\Meta\UserMeta;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Kra8\Snowflake\HasShortflakePrimary;
use Laravel\Sanctum\HasApiTokens;
use Mchev\Banhammer\Traits\Bannable;
use Plank\Metable\Metable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Squire\Models\Country;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, HasName, MustVerifyEmail
{
    use Bannable,
        HasApiTokens,
        HasFactory,
        HasRoles,
        HasShortflakePrimary,
        InteractsWithMedia,
        Metable,
        Notifiable;

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

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::squish($this->given_name . ' ' . $this->family_name),
        );
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
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
        if ($tenant->getKey() == Conference::active()?->getKey()) {
            return true;
        }

        return $this->can('view', $tenant);
    }

    public function canImpersonate()
    {
        return $this->can('User:loginAs');
    }

    public function canBeImpersonated()
    {
        if ($this->isBanned()) {
            return false;
        }

        if ($this->hasAnyRole([UserRole::Admin->value])) {
            return false;
        }

        return true;
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function submissionsAsParticipant()
    {
        return $this->belongsToMany(Submission::class, 'submission_has_participants', 'user_id', 'submission_id');
    }

    public function scopeEditorSubmission($query, Submission $submission)
    {
        $editorRoleId = Role::where('name', UserRole::Editor->value)->first()->getKey();

        return $query->whereHas('submissionsAsParticipant', function ($query) use ($submission, $editorRoleId) {
            $query->where('role_id', $editorRoleId)
            ->where('submission_id', $submission->getKey());
        });
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
            if (!$role->parent_id) {
                continue;
            }

            if (!$role->ancestors->pluck('id')->intersect($permission->roles->pluck('id')->toArray())->isEmpty()) {
                return true;
            }
        }

        return false;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->hasMedia('profile')) {
            return $this->getFirstMediaUrl('profile', 'avatar');
        }

        $name = str($this->fullName)
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=111827&font-size=0.33';
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar')
            ->keepOriginalImageFormat()
            ->width(50);

        $this->addMediaConversion('thumb')
            ->keepOriginalImageFormat()
            ->width(400);

        $this->addMediaConversion('thumb-xl')
            ->keepOriginalImageFormat()
            ->width(800);
    }

    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function asAuthor()
    {
        return Author::email($this->email)->first();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        Mail::to($this->getEmailForVerification())->send(new VerifyUserEmail($this));
    }
}
