<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use App\Models\Enums\ConferenceStatus;
use App\Models\Scopes\ConferenceScope;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceCloneAction
{
    use AsAction;

    public function handle(array $data)
    {
        try {
            DB::beginTransaction();

            $clonedDataConference = $this->cloneConference($data);

            $this->cloneRelations($clonedDataConference);

            DB::commit();

            return $clonedDataConference;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function cloneConference(array $data): Conference
    {
        $clonedConferenceId = $data['conference_id'];

        $clonedDataConference = Conference::with([
            'meta',

            'topics' => function ($query) {
                $query->withoutGlobalScopes([ConferenceScope::class]);
            },

            'staticPages' => function ($query) {
                $query->withoutGlobalScopes([ConferenceScope::class]);
            }
        ])->findOrFail($clonedConferenceId)->replicate();

        $clonedDataConference->fill($data);
        $clonedDataConference->status = ConferenceStatus::Upcoming;
        $clonedDataConference->path = $this->generateUniquePath($clonedDataConference->path);
        $clonedDataConference->save();

        return $clonedDataConference;
    }

    private function cloneRelations(Conference $clonedDataConference): void
    {
        $this->cloneTopics($clonedDataConference);
        $this->cloneStaticPages($clonedDataConference);
        $this->cloneMeta($clonedDataConference);
    }

    private function cloneTopics(Conference $clonedDataConference): void
    {
        foreach ($clonedDataConference->topics as $topic) {
            $clonedTopic = $topic->replicate();
            $clonedTopic->conference_id = $clonedDataConference->id;
            $clonedTopic->save();
        }
    }

    private function cloneStaticPages(Conference $clonedDataConference): void
    {
        foreach ($clonedDataConference->staticPages as $staticPage) {
            $clonedStaticPage = $staticPage->replicate();
            $clonedStaticPage->conference_id = $clonedDataConference->id;
            $clonedStaticPage->save();
        }
    }

    private function cloneMeta(Conference $clonedDataConference): void
    {
        foreach ($clonedDataConference->meta as $meta) {
            $clonedMeta = $meta->replicate();
            $clonedMeta->metable_id = $clonedDataConference->id;
            $clonedMeta->save();
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
