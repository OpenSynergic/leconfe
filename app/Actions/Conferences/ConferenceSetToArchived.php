<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceSetToArchived
{
    use AsAction;

    public function handle()
    {
        Conference::where('end_at', now())->update([
            'status' => ConferenceStatus::Archived,
        ]);
    }
}
