<?php

namespace App\Administration\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class Dashboard extends Page
{

    protected static ?string $navigationIcon = 'heroicon-m-home';

    protected static ?string $title = 'Administration';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string $view = 'administration.pages.dashboard';

    public function clearDataCache()
    {
        try {
            // Artisan::call('cache:clear');
        } catch (\Throwable $th) {
        }
    }

    public function clearTemplateCaches()
    {
        try {
            Action::make('delete')
                ->action(fn () => dd('hehehe'))
                ->requiresConfirmation();
            Artisan::call('view:clear');
            Notification::make()
                ->title('Clear successfully')
                ->success()
                ->body('Compiled views cleared successfully')
                ->send();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function expireUserSession()
    {
        try {
            Auth::logout();
            Session::flush();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', id: 'close');
    }
}
