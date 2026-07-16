<?php

namespace App\Actions\Topics;

use Throwable;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class TopicCreateAction
{
    use AsAction;

    public function handle($data): Topic
    {
        try {
            DB::beginTransaction();

            $topic = Topic::create($data);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $topic;
    }
}
