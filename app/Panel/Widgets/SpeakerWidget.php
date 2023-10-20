<?php

namespace App\Panel\Widgets;

use App\Models\Participant;
use Filament\Widgets\Widget;

class SpeakerWidget extends Widget
{
    protected static string $view = 'panel.widgets.speaker-widget';

    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        $participants = Participant::whereHas('meta', function ($query) {
            $query->where('key', 'confirmed')->where('value', true);
        })->get();

        return ['participants' => $participants->isEmpty() ? [] : $participants];
        
    } 
    
  
}
 