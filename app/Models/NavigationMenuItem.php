<?php

namespace App\Models;

use App\Models\Enums\NavigationMenuItemType;
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

    public function getUrl()
    {
        $typeEnum = NavigationMenuItemType::tryFromName($this->type);
    }
}
