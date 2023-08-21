<?php

namespace App\Actions\Committee;

use App\Models\Committee;
use App\Models\CommitteeMember;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CommitteInsertAction
{
    use AsAction;

    public function handle($data)
    {
        try {
            $committee = Committee::create($data);
        } catch (\Throwable $th) {
            throw $th;
        }

        return $committee;
    }
}