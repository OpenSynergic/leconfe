<?php

namespace App\Actions\Conferences;

use App\Models\Topic;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTopicAction
{
    use AsAction;

    public function handle($data)
    {
        $topic = Topic::create($data);

        return $topic;
    }
}
