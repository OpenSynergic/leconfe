<?php

namespace App\Facades;

use App\Classes\Sidebar;
use App\Managers\SidebarManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(Sidebar | array $blocks)
 * @method static \Illuminate\Support\Collection getBlocks(bool $onlyActive = true)
 */
class SidebarFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SidebarManager::class;
    }
}
