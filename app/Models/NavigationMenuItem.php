<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Metable\Metable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class NavigationMenuItem extends Model implements Sortable
{
    use HasFactory, HasRecursiveRelationships, Metable, SortableTrait;

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
            'remote-url' => NavigationItemType\RemoteUrl::class,
            'about' => NavigationItemType\About::class,
            'announcements' => NavigationItemType\Announcements::class,
            'dashboard' => NavigationItemType\Dashboard::class,
            'home' => NavigationItemType\Home::class,
            'login' => NavigationItemType\Login::class,
            'logout' => NavigationItemType\Logout::class,
            'proceedings' => NavigationItemType\Proceedings::class,
            'profile' => NavigationItemType\Profile::class,
            'register' => NavigationItemType\Register::class,
            'search' => NavigationItemType\Search::class,
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
