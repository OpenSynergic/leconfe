<?php

namespace App\Actions\Speakers;

use App\Models\Speaker;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SpeakerDeleteAction
{
    use AsAction;

    public function handle(Speaker $speaker)
    {
        try {
            DB::beginTransaction();

            $speaker->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
