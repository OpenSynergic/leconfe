<?php

namespace Database\Seeders\Productions;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([Role::ADMIN, Role::AUTHOR, Role::EDITOR, Role::REVIEWER] as $role) {
            Role::create(['name' => $role]);
        }
    }
}
