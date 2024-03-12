<?php

namespace App\Models;

use App\Models\Enums\NavigationMenuItemType;
use App\Models\NavigationItemType;
use App\Models\NavigationItemType\BaseNavigationItemType;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Plank\Metable\Metable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class NavigationMenuItem extends Model implements Sortable
{
    use HasFactory, Metable, SortableTrait, HasRecursiveRelationships;

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
            $model->descendants()->delete();
        });
    }

    public function navigationMenu()
    {
        return $this->belongsTo(NavigationMenu::class);
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
            'remote-url' => new NavigationItemType\RemoteUrl,
            'about' => new NavigationItemType\About,
            'announcements' => new NavigationItemType\Announcements,
            'dashboard' => new NavigationItemType\Dashboard,
            'home' => new NavigationItemType\Home,
            'login' => new NavigationItemType\Login,
            'logout' => new NavigationItemType\Logout,
            'proceedings' => new NavigationItemType\Proceedings,
            'profile'=> new NavigationItemType\Profile,
            'register' => new NavigationItemType\Register,
        ];
    }

    public static function getType(string $type): BaseNavigationItemType
    {
        return self::getTypes()[$type];
    }

    public static function getTypeOptions(): array
    {
        return collect(self::getTypes())
            ->mapWithKeys(fn($type) => [$type->getId() => $type->getLabel()])
            ->toArray();
    }

    public function getUrl(): string
    {
        return self::getType($this->type)->getUrl($this) ?? '#';
    }

    public function getLabel(): string
    {
        // replace {$username} with the user's name
        if (strpos($this->label, '{$username}') !== false) {
            $this->label = str_replace('{$username}', auth()->user()->fullName, $this->label);
        }

        return $this->label;
    }

    public function isDisplayed(): bool
    {
        return self::getType($this->type)->getIsDisplayed($this);
    }
}
