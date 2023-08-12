<?php

namespace App\Actions\Conferences;

use App\Models\Speaker;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSpeakerAction
{
    use AsAction;

    public function handle($data)
    {
        try {
            $speaker = Speaker::create($data);

            return $speaker;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
