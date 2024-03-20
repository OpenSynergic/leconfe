<?php

namespace App\Panel\Conference\Resources;

use App\Panel\Conference\Resources\Traits\CustomizedUrl;
use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    use CustomizedUrl;
}
