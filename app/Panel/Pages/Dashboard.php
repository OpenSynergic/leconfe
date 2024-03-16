<?php

namespace App\Panel\Pages;

use App\Panel\Pages\Traits\CustomizedUrl;
use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{
    use CustomizedUrl;
}
