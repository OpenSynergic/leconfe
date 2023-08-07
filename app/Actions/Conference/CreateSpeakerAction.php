<?php

namespace App\Actions\Conference;

use App\Models\Speaker;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use PhpParser\Node\Stmt\TryCatch;

class CreateSpeakerAction
{
    use AsAction;

    public function handle($data)
    {
       $speaker = Speaker::create($data);

       return $speaker;
    }
}
