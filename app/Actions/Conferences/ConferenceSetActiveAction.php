<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceSetActiveAction
{
    use AsAction;

    public function handle(Conference $conference)
    {
        try {
            DB::beginTransaction();

            Conference::query()
                ->where('status', ConferenceStatus::Active->value)
                ->update(['status' => ConferenceStatus::Archived->value]);

            $conference->status = ConferenceStatus::Active;
            $conference->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
