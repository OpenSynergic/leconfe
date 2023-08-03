<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceSetCurrentAction
{
    use AsAction;

    public function handle(int|Conference $conferenceId)
    {
        setting()->set('current_conference', is_int($conferenceId) ? $conferenceId : $conferenceId->id);
    }
}
