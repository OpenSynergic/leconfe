<?php

namespace App\Actions\Committees;

use App\Models\Committee;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CommitteeUpdateAction
{
    use AsAction;

    public function handle(Committee $committee, array $data): Committee
    {
        try {
            DB::beginTransaction();

            $committee->update($data);

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
