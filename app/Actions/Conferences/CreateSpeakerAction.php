<?php

namespace App\Actions\Conferences;


use App\Models\Speaker;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use PhpParser\Node\Stmt\TryCatch;

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
