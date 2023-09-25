<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceCreateAction
{
    use AsAction;

    public function handle(array $data): Conference
    {
        try {
            DB::beginTransaction();

            $conference = Conference::create($data);

            if (data_get($data, 'meta')) {
                $conference->setManyMeta($data['meta']);
            }

            if (data_get($data, 'current')) {
                ConferenceSetActiveAction::run($conference);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $conference;
    }
}
