<?php

namespace App\Actions\NavigationMenu;

use App\Models\NavigationMenuItem;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNavigationMenuItemAction
{
    use AsAction;

    public function handle($data)
    {
        try {
            DB::beginTransaction();

            $navigationMenuItem = NavigationMenuItem::create($data);
            if (data_get($data, 'meta')) {
                $navigationMenuItem->setManyMeta(data_get($data, 'meta'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $navigationMenuItem;
    }
}
