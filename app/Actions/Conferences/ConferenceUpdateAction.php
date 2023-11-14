<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceUpdateAction
{
    use AsAction;

    public function handle(Conference $conference, array $data)
    {
        try {
            DB::beginTransaction();

            $conference->update($data);

            if (data_get($data, 'meta')) {
                $conference->setManyMeta(data_get($data, 'meta'));
            }

            if (data_get($data, 'active')) {
                ConferenceSetActiveAction::run($conference);
            }

            AppearanceUpdateAction::run($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $conference;
    }
}
