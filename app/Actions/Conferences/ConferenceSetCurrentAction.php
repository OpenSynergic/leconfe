<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceSetCurrentAction
{
    use AsAction;

    public function handle(Conference $conference)
    {
        try {
            DB::beginTransaction();

            Conference::query()->update(['is_current' => 0]);

            $conference->is_current = 1;
            $conference->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
