<?php

namespace App\Actions\Committee;

use App\Models\CommitteeMember;
use Lorisleiva\Actions\Concerns\AsAction;

class CommitteMemberInsertAction
{
    use AsAction;

    public function handle($data)
    {
        $committee_member = CommitteeMember::create($data);
        return $committee_member;
    }
}
