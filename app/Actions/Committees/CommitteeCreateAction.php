<?php

namespace App\Actions\Committees;

use App\Models\Committee;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CommitteeCreateAction
{
    use AsAction;

    public function handle(array $data): Committee
    {
        try {
            DB::beginTransaction();

            $committee = Committee::create($data);

            if (data_get($data, 'meta')) {
                $committee->setManyMeta(data_get($data, 'meta'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $committee;
    }
}
