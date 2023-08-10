<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceChangeStatusAction
{
    use AsAction;

    public function handle(Conference $conference, ConferenceStatus $status)
    {
        try {
            DB::beginTransaction();
            $conference->update([
                'status' => $status,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $conference;
    }
}
