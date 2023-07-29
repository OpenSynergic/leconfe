<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateConference
{
    use AsAction;

    public function handle(array $data, Conference $conference)
    {
        try {
            DB::beginTransaction();

            $conference->update($data);

            if (array_key_exists('meta', $data) && is_array($data['meta'])) {
                $conference->setManyMeta($data['meta']);
            }

            if (array_key_exists('current', $data) && $data['current'] === true) {
                SetCurrentConference::run($conference);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $conference;
    }
}
