<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $site = Site::create();

        $site->setManyMeta([
            'name' => 'Leconfe',
            'page_footer' => view('frontend.examples.footer')->render(),
        ]);
    }
}
