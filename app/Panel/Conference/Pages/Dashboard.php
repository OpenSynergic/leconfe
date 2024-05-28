<?php

namespace App\Panel\Conference\Pages;

use App\Mail\Templates\TestMail;
use App\Models\DOI;
use App\Models\Proceeding;
use Filament\Pages\Dashboard as PagesDashboard;
use Illuminate\Support\Facades\Mail;

class Dashboard extends PagesDashboard
{
    public function mount()
    {
        Mail::to(auth()->user())->send(new TestMail);
    }


}
