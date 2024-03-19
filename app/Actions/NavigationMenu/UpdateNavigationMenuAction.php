<?php

namespace App\Actions\NavigationMenu;

use App\Models\NavigationMenu;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNavigationMenuAction
{
    use AsAction;

    public function handle(NavigationMenu $navigationMenu, $data)
    {
        try {
            DB::beginTransaction();

            $navigationMenu->update($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $navigationMenu;
    }
}
