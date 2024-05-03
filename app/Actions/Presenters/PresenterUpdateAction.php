<?php

namespace App\Actions\Presenters;

use App\Models\Presenter;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class PresenterUpdateAction
{
    use AsAction;

    public function handle(Presenter $presenter, array $data): Presenter
    {
        try {
            DB::beginTransaction();

            $presenter->update($data);

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
