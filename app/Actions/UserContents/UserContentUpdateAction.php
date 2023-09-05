<?php

namespace App\Actions\UserContents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UserContentUpdateAction
{
    use AsAction;

    public function handle(array $data, Model $userContent): Model
    {
        try {
            DB::beginTransaction();

            $userContent->update($data);
            
            unset($data['title']);

            $userContent->syncMeta($data);

            // if ($sendEmail) {
            //     // TODO Create a job to send email

            // }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $userContent;
    }
}
