<?php

namespace App\Panel\Conference\Widgets;

use App\Models\Participant;
use App\Models\ParticipantPosition;
use Filament\Widgets\Widget;

class SpeakerWidget extends Widget
{
    protected static string $view = 'panel.conference.widgets.speaker-widget';

    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        $participants_position = ParticipantPosition::where('type', 'speaker')->pluck('id');

        $participants = Participant::whereHas('positions', function ($query) use ($participants_position) {
            $query->whereIn('id', $participants_position);
        })->whereHas('meta', function ($query) {
            $query->where('key', 'confirmed')->where('value', true);
        })->get();

        return ['participants' => $participants];
    }
}
