<?php

namespace App\Models;

use App\Models\Enums\NavigationMenuItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
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

    public function getUrl() : string
    {
        $typeEnum = NavigationMenuItemType::tryFromName($this->type);
        $conferenceId = App::getCurrentConference()->id;
        return '#';
        // return match ($typeEnum) {
        //     NavigationMenuItemType::Home => $conferenceId ? route('livewirePageGroup.conference') route('livewirePageGroup.website.pages.home') ,
        //     NavigationMenuItemType::RemoteURL => $this->getMeta('url'),
        //     default, NavigationMenuItemType::Empty  => '#',
        // };
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isDisplayed(): bool
    {
        $typeEnum = NavigationMenuItemType::tryFromName($this->type);

        return $typeEnum?->isDisplayed() ?? true;
    }
}
