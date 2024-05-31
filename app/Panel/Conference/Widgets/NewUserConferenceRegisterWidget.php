<?php

namespace App\Panel\Conference\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;

class NewUserConferenceRegisterWidget extends Widget
{
    protected static string $view = 'panel.conference.widgets.new-user-conference-register-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }
    
    protected function getViewData(): array
    {
        return [
            'announcement' => Announcement::latest()->first(),
        ];
    }
}
