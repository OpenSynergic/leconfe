<?php

namespace Rahmanramsi\LivewirePageGroup\Components;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

abstract class Component
{
    use Conditionable;
    use Macroable;
    use Tappable;
}
