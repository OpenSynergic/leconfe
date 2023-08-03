<?php

namespace App\Actions\Authors;

use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AuthorCreateAction
{
    use AsAction;

    public function handle(Submission $submission, array $data)
    {
        try {
            DB::beginTransaction();

            $author = $submission->authors()->create($data);

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
