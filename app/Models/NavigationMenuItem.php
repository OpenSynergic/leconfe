<?php

namespace App\Models;

use App\Models\NavigationItemType\RemoteUrl;
use App\Models\NavigationItemType\About;
use App\Models\NavigationItemType\Contact;
use App\Models\NavigationItemType\Announcements;
use App\Models\NavigationItemType\Dashboard;
use App\Models\NavigationItemType\Home;
use App\Models\NavigationItemType\Login;
use App\Models\NavigationItemType\Logout;
use App\Models\NavigationItemType\Proceedings;
use App\Models\NavigationItemType\Profile;
use App\Models\NavigationItemType\Register;
use App\Models\NavigationItemType\Search;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Plank\Metable\Metable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class NavigationMenuItem extends Model implements Sortable
{
    use Cachable, HasFactory, Metable, SortableTrait;

    protected $fillable = [
        'label',
        'type',
        'navigation_menu_id',
        'parent_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Model $model) {
            $model->children()->delete();
        });
    }

    public function navigationMenu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function buildSortQuery()
    {
        return static::query()
            ->where('navigation_menu_id', $this->navigation_menu_id)
            ->where('parent_id', $this->parent_id);
    }

    public static function getTypes(): array
    {
        return [
            'remote-url' => RemoteUrl::class,
            'about' => About::class,
            'contact' => Contact::class,
            'announcements' => Announcements::class,
            'dashboard' => Dashboard::class,
            'home' => Home::class,
            'login' => Login::class,
            'logout' => Logout::class,
            'proceedings' => Proceedings::class,
            'profile' => Profile::class,
            'register' => Register::class,
            'search' => Search::class,
        ];
    }

    public static function getType(string $type): string
    {
        return self::getTypes()[$type];
    }

    public static function getTypeOptions(): array
    {
        return collect(self::getTypes())
            ->mapWithKeys(fn ($type) => [$type::getId() => $type::getLabel()])
            ->toArray();
    }

    public function getUrl(): string
    {
        return self::getType($this->type)::getUrl($this) ?? '#';
    }

    public function getLabel(): string
    {
        // replace {$username} with the user's name
        if (auth()->check() && strpos($this->label, '{$username}') !== false) {
            $this->label = str_replace('{$username}', auth()->user()->fullName, $this->label);
        }

        return $this->label;
    }

    public function isDisplayed(): bool
    {
        return self::getType($this->type)::getIsDisplayed($this);
    }
}
