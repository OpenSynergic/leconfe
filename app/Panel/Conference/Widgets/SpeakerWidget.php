<?php

namespace App\Panel\Conference\Widgets;

use App\Models\Speaker;
use App\Models\SpeakerRole;
use Filament\Widgets\Widget;

class SpeakerWidget extends Widget
{
    protected static string $view = 'panel.conference.widgets.speaker-widget';

    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        $speakerRoles = SpeakerRole::query()->pluck('id');

        $speakers = Speaker::whereIn('speaker_role_id', $speakerRoles)
            ->whereHas('meta', function ($query) {
                $query->where('key', 'confirmed')->where('value', true);
            })
            ->get();

        return ['speakers' => $speakers];
    }
}
