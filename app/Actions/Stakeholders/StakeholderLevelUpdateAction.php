<?php

namespace App\Actions\Stakeholders;

use Throwable;
use App\Models\StakeholderLevel;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class StakeholderLevelUpdateAction
{
    use AsAction;

    public function handle(StakeholderLevel $record, array $data): StakeholderLevel
    {
        try {
            DB::beginTransaction();

            $record->update($data);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $record;
    }
}
