<?php

namespace App\Actions\Leconfe;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\confirm;

class CheckVersionAction
{
    use AsAction;

    public function handle()
    {
        
    }

    public function asCommand(Command $command): void
    {

    }

    public function getCommandSignature(): string
    {
        return 'leconfe:check';
    }

    public function getCommandDescription(): string
    {
        return 'Check leconfe version';
    }
}
