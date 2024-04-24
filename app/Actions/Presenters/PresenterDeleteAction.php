<?php

namespace App\Actions\Presenters;

use App\Models\Presenter;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class PresenterDeleteAction
{
    use AsAction;

    public function handle(Presenter $presenter)
    {
        try {
            DB::beginTransaction();

            $presenter->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
