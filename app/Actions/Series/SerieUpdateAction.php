<?php

namespace App\Actions\Series;

use App\Models\Conference;
use App\Models\Serie;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SerieUpdateAction
{
    use AsAction;

    public function handle(Serie $serie, array $data) : Serie
    {
        try {
            DB::beginTransaction();

            $serie->update($data);

            if (data_get($data, 'meta')) {
                $serie->setManyMeta(data_get($data, 'meta'));
            }
            
            if(data_get($data, 'set_as_active')) {
                SerieSetAsActiveAction::run($serie);
            }


            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $serie;
    }
}
