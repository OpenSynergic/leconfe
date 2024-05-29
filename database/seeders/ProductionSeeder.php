<?php

namespace Database\Seeders;

use Database\Seeders\Productions\MailTemplateSeeder;
use Database\Seeders\Productions\PermissionSeeder;
use Database\Seeders\Productions\RoleSeeder;
use Database\Seeders\Productions\SubmissionFileTypeSeeder;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(SiteSeeder::class);
        $this->call(SubmissionFileTypeSeeder::class);
    }
}
