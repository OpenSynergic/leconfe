<?php

namespace App\Actions\Series;

use App\Models\Conference;
use App\Models\Serie;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SerieSetAsCurrentAction
{
    use AsAction;

    public function handle(Serie $serie): Serie
    {
        try {
            DB::beginTransaction();

            $serie->setAsCurrent();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $serie;
    }
}
