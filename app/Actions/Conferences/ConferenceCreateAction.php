<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Actions\Conferences\ConferenceCloneAction;

class ConferenceCreateAction
{
    use AsAction;

    public function handle(array $data): Conference
    {
        try {
            DB::beginTransaction();

            $conferenceData = null;

            if (data_get($data, 'conference_id')) {
                $conferenceData = ConferenceCloneAction::run($data);
            } else {
                $conferenceData = Conference::create($data);
            }

            if (data_get($data, 'meta')) {
                $conferenceData->setManyMeta($data['meta']);
            }

            if (data_get($data, 'active')) {
                ConferenceSetActiveAction::run($conferenceData);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $conferenceData;
    }
}
