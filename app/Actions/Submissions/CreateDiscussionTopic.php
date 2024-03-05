<?php

namespace App\Actions\Submissions;

use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateDiscussionTopic
{
    use AsAction;

    /**
     * Create a new discussion topic for the given submission.
     * and assign the given participants to the topic.
     *
     * @param  array  $participants  user ids
     */
    public function handle(Submission $submission, array $topicData = [], array $participants = [])
    {
        try {
            DB::beginTransaction();
            $discussionTopic = $submission->discussionTopics()->create($topicData);

            foreach ($participants as $participant) {
                $discussionTopic->participants()->create(['user_id' => $participant]);
            }

            DB::commit();

            return $discussionTopic;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
