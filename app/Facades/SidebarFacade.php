<?php

namespace App\Facades;

use Illuminate\Support\Collection;
use App\Classes\Sidebar;
use App\Managers\SidebarManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(Sidebar | array $blocks)
 * @method static Collection getBlocks(bool $onlyActive = true)
 */
class SidebarFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SidebarManager::class;
    }
}
