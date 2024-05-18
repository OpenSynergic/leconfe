<?php

namespace App\Actions\Series;

use App\Models\Conference;
use App\Models\Serie;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SerieSetAsActiveAction
{
    use AsAction;

    public function handle(Serie $serie): Serie
    {
        try {
            DB::beginTransaction();

            Serie::where('active', true)->update(['active' => false]);

            $serie->update(['active' => true]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $serie;
    }
}
