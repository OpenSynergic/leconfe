<?php

namespace Database\Seeders\Productions;

use App\Actions\MailTemplates\MailTemplatePopulateDefaultData;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            MailTemplatePopulateDefaultData::run();
        } catch (\Throwable $th) {
            throw $th;
        }

    }
}
