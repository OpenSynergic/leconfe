<?php

namespace App\Filament\Pages\Settings;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Workflow extends Page
{
    protected static ?string $navigationGroup = 'Settings';

    // TODO carikan icon untuk halaman workflow
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings.workflow';

    public function mount()
    {
    }
}
