<?php

namespace App\Actions\Presenters;

use App\Models\Presenter;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class PresenterCreateAction
{
    use AsAction;

    public function handle(Submission $submission, array $data)
    {
        try {
            DB::beginTransaction();

            $presenter = $submission->presenters()->create($data);

            if (data_get($data, 'meta')) {
                $presenter->setManyMeta(data_get($data, 'meta'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $presenter;
    }
}
