<?php

namespace App\Panel\Pages;

use App\Panel\Pages\Traits\CustomizedUrl;
use Filament\Pages\Page;

abstract class BasePage extends Page
{
    use CustomizedUrl;
}
