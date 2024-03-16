<?php

namespace App\Panel\Resources;

use App\Panel\Resources\Traits\CustomizedUrl;
use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    use CustomizedUrl;
}
