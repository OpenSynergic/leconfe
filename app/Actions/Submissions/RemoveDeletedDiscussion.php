<?php

namespace App\Actions\Submissions;

use App\Models\Discussion;
use App\Models\DiscussionTopic;
use App\Models\DiscussionTopicParticipant;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RemoveDeletedDiscussion
{
    use AsAction;

    public function handle()
    {
        try {
            DB::beginTransaction();
            DiscussionTopicParticipant::onlyTrashed()->forceDelete();
            Discussion::onlyTrashed()->forceDelete();
            DiscussionTopic::onlyTrashed()->forceDelete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
