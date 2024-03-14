<?php

namespace App\Panel\Resources;

use App\Administration\Resources\StaticPageResource as StaticPageResourceBase;
use App\Panel\Resources\Traits\CustomizedUrl;

class StaticPageResource extends StaticPageResourceBase
{
    protected static ?string $navigationGroup = 'Conferences';

    use CustomizedUrl;
}
