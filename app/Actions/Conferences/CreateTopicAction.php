<?php

namespace App\Actions\Conferences;

use App\Models\Topic;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTopicAction
{
    use AsAction;

    public function handle($data)
    {
        try {
            $topic = Topic::create($data);
        } catch (\Throwable $th) {
            throw $th;
        }

        return $topic;
    }
}