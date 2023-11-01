<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use App\Models\Enums\ConferenceStatus;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceCloneAction
{
    use AsAction;

    public function handle(array $data)
    {
        try {
            DB::beginTransaction();

            $clonedConferenceId = $data['conference_id'];

            // dd($clonedDataConference = Conference::with(['topics', 'meta', 'navigations'])->findOrFail(354817923763507)->replicate());
            dd($clonedDataConference = Conference::withoutGlobalScopes([ScopeConference::class])->with(['topics', 'meta', 'navigations'])->findOrFail(354817923763507)->replicate());


            $clonedDataConference = Conference::with(['topics', 'meta', 'navigations'])->findOrFail($clonedConferenceId)->replicate();

            dd($clonedDataConference);

            $clonedDataConference->fill($data);

            $clonedDataConference->status = ConferenceStatus::Upcoming;

            $clonedDataConference->path = $this->generateUniquePath($clonedDataConference->path);

            $clonedDataConference->save();

            // clone topic
            foreach ($clonedDataConference->topics as $topic) {
                $clonedTopic = $topic->replicate();
                $clonedTopic->conference_id = $clonedDataConference->id;
                $clonedTopic->save();
            }

            DB::commit();

            return $clonedDataConference;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function generateUniquePath($basePath)
    {
        $uniquePath = $basePath;
        $counter = 1;

        while (Conference::where('path', $uniquePath)->exists()) {
            $counter++;
            $uniquePath = $basePath . $counter;
        }

        return $uniquePath;
    }
}
