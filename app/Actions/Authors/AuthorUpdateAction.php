<?php

namespace App\Actions\Authors;

use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AuthorUpdateAction
{
    use AsAction;

    public function handle(array $data, Author $author): Author
    {
        try {
            DB::beginTransaction();

            $author->update($data);

            if (array_key_exists('meta', $data) && is_array($data['meta'])) {
                $author->setManyMeta($data['meta']);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $author;
    }
}
