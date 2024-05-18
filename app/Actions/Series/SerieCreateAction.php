<?php

namespace App\Actions\Series;

use App\Models\Conference;
use App\Models\Serie;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SerieCreateAction
{
    use AsAction;

    public function handle(array $data): Serie
    {
        try {
            DB::beginTransaction();

            $serie = Serie::create($data);

            if (data_get($data, 'meta')) {
                $serie->setManyMeta($data['meta']);
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
