<?php

namespace App\Actions\Speakers;

use App\Models\Speaker;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SpeakerUpdateAction
{
    use AsAction;

    public function handle(Speaker $speaker, array $data): Speaker
    {
        try {
            DB::beginTransaction();

            $speaker->update($data);

            if (data_get($data, 'meta')) {
                $speaker->setManyMeta(data_get($data, 'meta'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $speaker;
    }
}
