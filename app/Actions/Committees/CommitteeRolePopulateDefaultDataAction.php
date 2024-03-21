<?php

namespace App\Actions\Committees;

use App\Models\CommitteeRole;
use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CommitteeRolePopulateDefaultDataAction
{
    use AsAction;

    public function handle(Conference $conference): void
    {
        try {
            DB::beginTransaction();

            foreach ([
                'Chair',
                'Member',
            ] as $committeeRole) {
                CommitteeRole::firstOrCreate([
                    'name' => $committeeRole,
                    'type' => 'committee',
                    'conference_id' => $conference->getKey(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
