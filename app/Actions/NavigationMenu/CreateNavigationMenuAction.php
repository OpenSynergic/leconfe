<?php

namespace App\Actions\NavigationMenu;

use App\Models\NavigationMenu;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNavigationMenuAction
{
    use AsAction;

    public function handle($data)
    {
        try {
            DB::beginTransaction();

            $navigationMenu = NavigationMenu::create($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $navigationMenu;
    }
}
