<?php

namespace App\Utils\Enums;

enum UpgradeActionPriority: int
{
    case HIGH = 1;
    case MEDIUM = 2;
    case LOW = 3;
}
