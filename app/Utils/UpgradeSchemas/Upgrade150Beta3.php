<?php

namespace App\Utils\UpgradeSchemas;

use Illuminate\Support\Facades\Artisan;

class Upgrade150Beta3 extends UpgradeBase
{
    public function run(): void
    {
        Artisan::call('migrate', [
            '--force' => true,
        ]);
    }
}
