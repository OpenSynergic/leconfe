<?php

namespace Database\Seeders;

use Database\Seeders\Productions\PermissionSeeder;
use Database\Seeders\Productions\RoleSeeder;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SiteSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
    }
}
