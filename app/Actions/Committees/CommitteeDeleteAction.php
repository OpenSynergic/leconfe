<?php

namespace App\Actions\Committees;

use App\Models\Committee;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CommitteeDeleteAction
{
    use AsAction;

    public function handle(Committee $committee)
    {
        try {
            DB::beginTransaction();

            $committee->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
