<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceSetCurrentAction
{
    use AsAction;

    public function handle(Conference $conference)
    {
        try {
            DB::beginTransaction();

            Conference::query()
                ->where('status', ConferenceStatus::Current->value)
                ->update(['status' => ConferenceStatus::Archived->value]);

            $conference->status = ConferenceStatus::Current;
            $conference->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
