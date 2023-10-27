<?php

namespace App\Administration\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class Dashboard extends Page
{

    protected static ?string $navigationIcon = 'heroicon-m-home';

    protected static ?string $title = 'Administration';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string $view = 'administration.pages.dashboard';

    public function clearDataCache()
    {
        try {
            Artisan::call('cache:clear');
            session()->flash('success', 'Cache cleared successfully!');
        } catch (\Throwable $th) {
        }
    }

    public function clearTemplateCaches()
    {
        try {
            Artisan::call('view:clear');
            session()->flash('success', 'Compiled views cleared successfully');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function expireUserSession()
    {
        try {
            Auth::logout();
            Session::flush();
            session()->flash('success', 'User session cleared succesfully');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
