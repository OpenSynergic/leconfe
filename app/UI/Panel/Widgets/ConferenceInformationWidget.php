<?php

namespace App\UI\Panel\Widgets;

use Filament\Widgets\Widget;

class ConferenceInformationWidget extends Widget
{
    protected static string $view = 'u-i.panel.widgets.conference-information-widget';

    protected int|string|array $columnSpan = 'full';
}
