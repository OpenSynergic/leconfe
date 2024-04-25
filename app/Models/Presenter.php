<?php

namespace App\Models;

use App\Models\Enums\PresenterStatus;
use App\Models\Meta\PresenterMeta;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Kra8\Snowflake\HasShortflakePrimary;
use Plank\Metable\Metable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Presenter extends Model implements HasAvatar, HasMedia, Sortable
{
    use HasShortflakePrimary, Metable, Notifiable, SortableTrait, InteractsWithMedia;

    protected $table = 'presenters';

    protected $fillable = [
        'presenter_role_id',
        'email',
        'given_name',
        'family_name',
        'public_name',
        'status',
    ];

    protected $casts = [
        'status' => PresenterStatus::class,
    ];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::squish($this->given_name.' '.$this->family_name),
        );
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

    protected function getMetaClassName(): string
    {
        return PresenterMeta::class;
    }

    public function scopeEmail(Builder $query, string $email)
    {
        return $query->where('email', $email);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($profilePicture = $this->getFirstMedia('profile')?->getAvailableUrl(['thumb', 'thumb-xl'])) {
            return $profilePicture;
        }

        $name = Str::of($this->fullName)
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=FFFFFF&background=111827&font-size=0.33';
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
