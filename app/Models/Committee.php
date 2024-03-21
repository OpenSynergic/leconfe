<?php

namespace App\Models;

use App\Models\Concerns\BelongsToConference;
use Plank\Metable\Metable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Meta\CommitteeMeta;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\CommitteeFactory;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Kra8\Snowflake\HasShortflakePrimary;
use Illuminate\Database\Eloquent\Builder;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Committee extends Model implements HasAvatar, HasMedia, Sortable
{
    use HasFactory, BelongsToConference, HasShortflakePrimary, Metable, Notifiable, SortableTrait, InteractsWithMedia;

    protected $table = 'committees';

    protected $fillable = [
        'committee_role_id',
        'email',
        'given_name',
        'family_name',
        'public_name',
    ];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::squish($this->given_name.' '.$this->family_name),
        );
    }

    protected static function newFactory(): Factory
    {
        return CommitteeFactory::new();
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
        return CommitteeMeta::class;
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(CommitteeRole::class, 'committee_role_id', 'id');
    }
}
