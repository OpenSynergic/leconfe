<?php

namespace App\Exceptions\Upgrade;

use Exception;

class NoUpgradeScript extends Exception
{
    public function __construct($installedVersion, $codeVersion)
    {
        parent::__construct("No upgrade script needed between for version $installedVersion to $codeVersion.");
    }
}