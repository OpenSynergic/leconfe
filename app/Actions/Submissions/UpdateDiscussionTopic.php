<?php

namespace App\Actions\Submissions;

use App\Models\DiscussionTopic;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateDiscussionTopic
{
    use AsAction;

    public function handle(DiscussionTopic $topic, array $topicData, array $participants)
    {
        try {
            DB::beginTransaction();
            $topic->update($topicData);

            $topic->participants()->delete();

            foreach ($participants as $participant) {
                $topic->participants()->create(['user_id' => $participant]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
