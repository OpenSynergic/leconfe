<?php

namespace App\Actions\Committees;

use App\Models\CommitteeRole;
use App\Models\Serie;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CommitteeRolePopulateDefaultDataAction
{
    use AsAction;

    public function handle(Serie $serie): void
    {
        try {
            DB::beginTransaction();

            foreach ([
                'Chair',
                'Member',
            ] as $committeeRole) {
                CommitteeRole::firstOrCreate([
                    'name' => $committeeRole,
                    'serie_id' => $serie->getKey(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
