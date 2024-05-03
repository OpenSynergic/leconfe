<?php

namespace App\Actions\Authors;

use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AuthorDeleteAction
{
    use AsAction;

    public function handle(Author $author)
    {
        try {
            DB::beginTransaction();

            $author->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
